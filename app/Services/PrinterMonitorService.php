<?php

namespace App\Services;

use App\Models\Printer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

/**
 * يفحص حالة طابعة Windows فعلياً عبر Win32_Printer (WMI/CIM) — أدق من مجرد استخدامها/عدمه،
 * لأنه يكشف تفاصيل حقيقية (نفاد ورق، نفاد حبر، باب مفتوح، انحشار ورق، عدم اتصال).
 */
class PrinterMonitorService
{
    /**
     * ترجمة أكواد Win32_Printer.DetectedErrorState القياسية
     * https://learn.microsoft.com/windows/win32/cimwin32prov/win32-printer
     */
    private const ERROR_STATE_MAP = [
        0 => ['healthy' => true, 'label' => 'غير معروف'],
        1 => ['healthy' => true, 'label' => 'أخرى'],
        2 => ['healthy' => true, 'label' => 'لا يوجد خطأ'],
        3 => ['healthy' => false, 'label' => 'الورق على وشك النفاد'],
        4 => ['healthy' => false, 'label' => 'نفاد الورق'],
        5 => ['healthy' => false, 'label' => 'الحبر/التونر على وشك النفاد'],
        6 => ['healthy' => false, 'label' => 'نفاد الحبر/التونر'],
        7 => ['healthy' => false, 'label' => 'باب الطابعة مفتوح'],
        8 => ['healthy' => false, 'label' => 'انحشار ورق'],
        9 => ['healthy' => false, 'label' => 'الطابعة غير متصلة (Offline)'],
        10 => ['healthy' => false, 'label' => 'الطابعة تحتاج صيانة'],
        11 => ['healthy' => false, 'label' => 'الذاكرة ممتلئة'],
    ];

    /**
     * @return array{status: string, is_healthy: bool, detail: string}
     */
    public function check(Printer $printer): array
    {
        $escapedName = str_replace(['\\', "'"], ['\\\\', "''"], $printer->windows_printer_name);

        $psCommand = "Get-CimInstance Win32_Printer -Filter \"Name='{$escapedName}'\" | "
            . "Select-Object WorkOffline, DetectedErrorState | ConvertTo-Json -Compress";

        $result = Process::timeout(20)->run([
            'powershell', '-NoProfile', '-Command', $psCommand,
        ]);

        if (!$result->successful()) {
            Log::warning("PrinterMonitor: PowerShell check failed for {$printer->name}", [
                'error' => $result->errorOutput(),
            ]);
            return [
                'status' => 'unknown',
                'is_healthy' => false,
                'detail' => 'تعذّر تنفيذ فحص الطابعة عبر PowerShell',
            ];
        }

        $output = trim($result->output());
        $data = json_decode($output, true);

        if (!is_array($data) || !array_key_exists('WorkOffline', $data)) {
            return [
                'status' => 'unknown',
                'is_healthy' => false,
                'detail' => "لم يتم العثور على طابعة باسم Windows: {$printer->windows_printer_name}",
            ];
        }

        $errorState = (int) ($data['DetectedErrorState'] ?? 0);
        $errorInfo = self::ERROR_STATE_MAP[$errorState] ?? ['healthy' => false, 'label' => "حالة غير معروفة ({$errorState})"];

        $workOffline = (bool) ($data['WorkOffline'] ?? false);

        if ($workOffline) {
            return [
                'status' => 'offline',
                'is_healthy' => false,
                'detail' => 'الطابعة غير متصلة (Work Offline)',
            ];
        }

        if (!$errorInfo['healthy']) {
            return [
                'status' => 'error',
                'is_healthy' => false,
                'detail' => $errorInfo['label'],
            ];
        }

        return [
            'status' => 'healthy',
            'is_healthy' => true,
            'detail' => $errorInfo['label'],
        ];
    }

    /**
     * يتحقق من قائمة انتظار الطباعة الفعلية في Windows (عبر Get-PrintJob) بعد إرسال أمر طباعة —
     * أكثر موثوقية بكثير من check() أعلاه (المعتمد على Win32_Printer.DetectedErrorState) لأن ذلك
     * الحقل يعتمد على دعم تعريف الطابعة لإرسال حالة الخطأ فعلياً عبر WMI، وقد ثبت عملياً أن بعض
     * التعريفات (لوحظ فعلياً مع Canon MF4700 UFRII LT XPS) لا تُحدّثه إطلاقاً حتى عند نفاد الورق
     * الفعلي — تبقى الحالة "لا يوجد خطأ" رغم توقف الطباعة فعلياً. قائمة انتظار الطباعة نفسها، بالمقابل،
     * يديرها Windows مباشرة بصرف النظر عن دعم التعريف: أي مهمة عالقة (لم تكتمل ولم تُحذف) تبقى ظاهرة
     * فيها، وهذا مؤشر موثوق بغض النظر عن نوع الطابعة أو جودة تعريفها.
     *
     * @return array{stuck: bool, detail: ?string}
     */
    public function waitForSpoolerClear(Printer $printer, int $maxWaitSeconds = 8): array
    {
        $escapedName = str_replace("'", "''", $printer->windows_printer_name);
        $psCommand = "Get-PrintJob -PrinterName '{$escapedName}' -ErrorAction SilentlyContinue | "
            . "Select-Object Id, JobStatus, PagesPrinted, TotalPages | ConvertTo-Json -Compress";

        $deadline = time() + $maxWaitSeconds;
        $lastJobStatus = null;

        while (time() < $deadline) {
            $result = Process::timeout(10)->run(['powershell', '-NoProfile', '-Command', $psCommand]);

            if (!$result->successful()) {
                Log::warning("PrinterMonitor: spooler queue check failed for {$printer->name}", [
                    'error' => $result->errorOutput(),
                ]);
                // تعذّر الفحص لا يعني بالضرورة أن المهمة عالقة — لا نُفشل الطباعة بسبب فشل فحص فرعي
                return ['stuck' => false, 'detail' => null];
            }

            $output = trim($result->output());
            if ($output === '') {
                // قائمة الانتظار فارغة = خرجت المهمة منها فعلياً (اكتملت الطباعة أو أُلغيت)
                return ['stuck' => false, 'detail' => null];
            }

            $data = json_decode($output, true);
            // Get-PrintJob لمهمة واحدة فقط تُعيد كائناً مباشرة (بلا تغليف كمصفوفة) من ConvertTo-Json
            $jobs = (is_array($data) && array_key_exists('Id', $data)) ? [$data] : (is_array($data) ? $data : []);

            if (empty($jobs)) {
                return ['stuck' => false, 'detail' => null];
            }

            $lastJobStatus = $jobs[0]['JobStatus'] ?? null;
            usleep(1000000);
        }

        return [
            'stuck' => true,
            'detail' => $lastJobStatus
                ? "المهمة ما زالت عالقة في قائمة الطباعة (الحالة: {$lastJobStatus}) — تحقق من الورق/الحبر/اتصال الطابعة"
                : 'المهمة عالقة في قائمة الطباعة ولم تكتمل — تحقق من الورق/الحبر/اتصال الطابعة',
        ];
    }
}
