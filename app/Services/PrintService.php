<?php

namespace App\Services;

use App\Models\PrintJob;
use Exception;
use Illuminate\Support\Facades\Process;

/**
 * يرسل ملفات PDF فعلياً إلى طابعة Windows عبر SumatraPDF (طباعة صامتة بلا نافذة).
 * دعم أنواع ملفات أخرى (Word/Excel/صور/طابعات حرارية) مُخطَّط له لاحقاً — انظر دراسة الجدوى.
 */
class PrintService
{
    public function __construct(private PrinterMonitorService $printerMonitor)
    {
    }

    public function print(PrintJob $job): void
    {
        if (!config('printing.enabled')) {
            throw new Exception('ميزة الطباعة الذكية غير مفعّلة (PRINTING_ENABLED=false في .env)');
        }

        if (!$job->printer) {
            throw new Exception('لا توجد طابعة محددة لهذه المهمة');
        }

        if (!file_exists($job->file_path)) {
            throw new Exception("الملف غير موجود محلياً: {$job->file_path}");
        }

        $extension = strtolower(pathinfo($job->file_path, PATHINFO_EXTENSION));
        if ($extension !== 'pdf') {
            throw new Exception("نوع الملف '{$extension}' غير مدعوم للطباعة الآلية حالياً (PDF فقط في هذه النسخة)");
        }

        if ($job->printer->type !== 'document') {
            throw new Exception("نوع الطابعة '{$job->printer->type}' غير مدعوم بعد (مخصص للتوسعة المستقبلية)");
        }

        $sumatraPath = config('printing.sumatra_path');
        if (!file_exists($sumatraPath)) {
            throw new Exception("SumatraPDF غير موجود في: {$sumatraPath}. يجب تنزيله ووضعه في هذا المسار (راجع دليل التشغيل).");
        }

        // [Fix 2026-08-05] فحص صحة الطابعة (WMI DetectedErrorState) وفحص قائمة انتظار الطباعة بعد
        // الإرسال (Get-PrintJob) كلاهما ثبت عملياً — عبر فحص حي مباشر مع المستخدم، وليس افتراضاً —
        // أنهما لا يكشفان أعطال بعض الطابعات (تحديداً الطابعات المتصلة عبر منافذ USB/MFNP العامة مثل
        // Canon UFRII LT): الطابعة تستقبل المستند كاملاً في ذاكرتها الداخلية فتُخرجه من قائمة الانتظار
        // فوراً وتُبقي DetectedErrorState سليماً، بينما "انتظار الورق" الفعلي يحدث داخلها بطريقة غير
        // مرئية تماماً لأي واجهة Windows. لذلك نُنفّذ هذين الفحصين فقط للطابعات التي أكّد المسؤول
        // يدوياً أنها تدعم إبلاغاً موثوقاً (Printer::supports_status_check) — لتفادي إرسال "تم بنجاح"
        // كاذب. الطابعات غير المؤكَّدة تُطبع بالاعتماد على رمز خروج SumatraPDF فقط (أفضل إشارة متاحة).
        $canVerifyHealth = (bool) $job->printer->supports_status_check;

        if ($canVerifyHealth) {
            $preCheck = $this->printerMonitor->check($job->printer);
            if (!$preCheck['is_healthy']) {
                throw new Exception("الطابعة غير جاهزة حالياً: {$preCheck['detail']}");
            }
        }

        $result = Process::timeout(config('printing.print_timeout', 60))->run([
            $sumatraPath,
            '-print-to', $job->printer->windows_printer_name,
            '-silent',
            '-print-settings', 'fit',
            $job->file_path,
        ]);

        if (!$result->successful()) {
            throw new Exception('فشل تنفيذ أمر الطباعة: ' . trim($result->errorOutput() ?: $result->output()));
        }

        if ($canVerifyHealth) {
            $spoolerCheck = $this->printerMonitor->waitForSpoolerClear($job->printer);
            if ($spoolerCheck['stuck']) {
                throw new Exception($spoolerCheck['detail']);
            }
        }
    }
}
