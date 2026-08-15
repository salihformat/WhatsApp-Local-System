<?php

namespace App\Services;

use App\Jobs\ProcessPrintJob;
use App\Models\Message;
use App\Models\PrintJob;
use App\Models\Printer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * نقطة إنشاء موحّدة لأي مهمة طباعة (سواء مصدرها مرفق واتساب وارد أو مجلد المراقبة المحلي)،
 * تحترم وضع الطابعة المستهدفة (Printer::print_mode): تلقائي يُطبع فوراً، أو "يتطلب موافقة"
 * يُحجز بحالة awaiting_approval وينتظر موافقة صريحة عبر لوحة التحكم أو رد واتساب من المسؤول.
 */
class PrintJobDispatcher
{
    public function __construct(private AdminNotifier $adminNotifier)
    {
    }

    public function dispatch(Message $message, Printer $printer, string $source): PrintJob
    {
        $printer = $this->resolveHealthyPrinter($printer);

        $extension = FileTypeResolver::resolveExtension($message->file_name, $message->file_path, $message->file_type, 'pdf');
        $fileName = $message->file_name ?: "ملف_{$message->id}.{$extension}";

        $printJob = PrintJob::create([
            'message_id' => $message->id,
            'printer_id' => $printer->id,
            'file_name' => $fileName,
            'file_path' => $message->file_path,
            'file_type' => $message->file_type,
            'status' => $printer->needsApproval() ? 'awaiting_approval' : 'pending',
            'source' => $source,
        ]);

        if ($printer->needsApproval()) {
            $this->notifyAdminForApproval($printJob);
        } else {
            dispatch(new ProcessPrintJob($printJob->id));
        }

        return $printJob;
    }

    /**
     * إنشاء مهمة طباعة من ملف محلي مباشر (مصدره مجلد الطباعة المستقل C:\PrintMonitor\print\<طابعة>،
     * بلا أي رسالة واتساب مرتبطة) — بخلاف dispatch() أعلاه، لا يوجد Message هنا إطلاقاً. $processingPath
     * هو مسار الملف الأصلي بعد نقله لمجلد processing الخاص بالطابعة (يُحفظ في source_file_path لنقله
     * لاحقاً إلى archive/failed عند اكتمال المهمة أو فشلها أو رفضها، عبر PrintFolderManager).
     */
    public function dispatchFromFile(string $processingPath, string $fileName, ?string $fileType, Printer $printer, string $source): PrintJob
    {
        $printer = $this->resolveHealthyPrinter($printer);

        $printJob = PrintJob::create([
            'message_id' => null,
            'printer_id' => $printer->id,
            'file_name' => $fileName,
            'file_path' => $processingPath,
            'source_file_path' => $processingPath,
            'file_type' => $fileType,
            'status' => $printer->needsApproval() ? 'awaiting_approval' : 'pending',
            'source' => $source,
        ]);

        if ($printer->needsApproval()) {
            $this->notifyAdminForApproval($printJob);
        } else {
            dispatch(new ProcessPrintJob($printJob->id));
        }

        return $printJob;
    }

    /**
     * [تحويل تلقائي عند التعطل] إن كانت الطابعة المطلوبة غير سليمة حسب آخر فحص دوري (monitor:printers،
     * راجع Printer::isHealthy()) ولها طابعة احتياطية مُعرَّفة (fallback_printer_id) وهي بدورها سليمة
     * ومفعّلة، نحوّل المهمة إليها تلقائياً بدل تعليقها بانتظار تدخل يدوي — مع تنبيه المسؤول بالتحويل
     * حتى يتابع إصلاح الطابعة الأصلية. لا سلسلة تحويل متعددة القفزات عمداً (خطوة واحدة فقط) لتفادي
     * أي احتمال لولب لا نهائي في حال أُعدّت طابعتان كل منهما احتياطية للأخرى بالخطأ.
     */
    private function resolveHealthyPrinter(Printer $printer): Printer
    {
        if ($printer->isHealthy()) {
            return $printer;
        }

        $fallback = $printer->fallbackPrinter;
        if (!$fallback || !$fallback->is_active || !$fallback->isHealthy()) {
            return $printer;
        }

        Log::warning("PrintJobDispatcher: printer '{$printer->name}' is unhealthy ({$printer->last_status_detail}) — failing over to backup printer '{$fallback->name}'.");

        // تنبيه واحد فقط لكل ساعة لنفس زوج الطابعتين، بدل رسالة واتساب مستقلة لكل ملف يصل أثناء
        // استمرار العطل (قد تكون عشرات الملفات في نفس الساعة من مجلد مراقبة نشط).
        $cooldownKey = "printer_failover_alert_{$printer->id}_{$fallback->id}";
        if (!Cache::has($cooldownKey)) {
            $this->adminNotifier->notify(
                "🔄 تحويل تلقائي للطباعة\nالطابعة \"{$printer->name}\" غير سليمة حالياً ({$printer->last_status_detail}) — تم تحويل مهام الطباعة الجديدة تلقائياً للطابعة الاحتياطية \"{$fallback->name}\".\nيرجى متابعة إصلاح الطابعة الأصلية.",
                ['source' => 'printer_failover', 'printer_id' => $printer->id, 'fallback_printer_id' => $fallback->id]
            );
            Cache::put($cooldownKey, true, now()->addHour());
        }

        return $fallback;
    }

    /**
     * موافقة على مهمة طباعة محجوزة (سواء من زر في لوحة التحكم أو رد واتساب "وافق <رقم>")
     * وتنفيذها فعلياً عبر ProcessPrintJob.
     */
    public function approve(PrintJob $printJob): bool
    {
        if ($printJob->status !== 'awaiting_approval') {
            return false;
        }

        $printJob->update(['status' => 'pending']);
        dispatch(new ProcessPrintJob($printJob->id));

        Log::info("PrintJob {$printJob->id}: approved and dispatched for printing");

        return true;
    }

    /**
     * رفض مهمة طباعة محجوزة دون طباعتها.
     */
    public function reject(PrintJob $printJob, ?string $reason = null): bool
    {
        if ($printJob->status !== 'awaiting_approval') {
            return false;
        }

        $printJob->update([
            'status' => 'rejected',
            'error_message' => $reason ?: 'تم الرفض من قبل المسؤول.',
        ]);

        Log::info("PrintJob {$printJob->id}: rejected", ['reason' => $reason]);

        // مهام مصدرها مجلد الطباعة المستقل (لا ProcessPrintJob سيُشغَّل لها أبداً بعد الرفض) —
        // ننقل ملفها الأصلي لمجلد failed فوراً هنا بدل انتظار دورة معالجة لن تأتي.
        app(PrintFolderManager::class)->moveToFailed($printJob);

        return true;
    }

    /**
     * موافقة على كل مهام الطباعة الحالية بانتظار الموافقة دفعة واحدة (زر "موافقة على الكل" أو أمر
     * واتساب "وافق الكل طباعة") — اختيارياً مقيّدة بطابعة واحدة فقط عبر $printerId.
     */
    public function approveAll(?int $printerId = null): int
    {
        $query = PrintJob::where('status', 'awaiting_approval');
        if ($printerId) {
            $query->where('printer_id', $printerId);
        }

        $count = 0;
        foreach ($query->get() as $printJob) {
            if ($this->approve($printJob)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * إبلاغ المسؤول (أو المسؤولين، راجع AdminNotifier) عبر واتساب بطلب طباعة بانتظار موافقته، مع
     * تعليمات الرد النصي البسيط للموافقة/الرفض (النظام لا يدعم أزرار تفاعلية حتى الآن). عامة (public)
     * لإعادة استخدامها من أمر التذكير التلقائي (بإضافة بادئة "⏰ تذكير" للطلبات المتأخرة عن الرد).
     */
    public function notifyAdminForApproval(PrintJob $printJob, bool $isReminder = false): void
    {
        $origin = $printJob->message
            ? "من: {$printJob->message->phone_number}"
            : 'المصدر: مجلد الطباعة المحلي';

        $prefix = $isReminder ? "⏰ تذكير — لا يزال بانتظار موافقتك:\n\n" : '';

        $text = $prefix . "🖨️ طلب طباعة بانتظار موافقتك\n"
            . "رقم المهمة: {$printJob->id}\n"
            . "{$origin}\n"
            . "الملف: {$printJob->file_name}\n"
            . "الطابعة: " . ($printJob->printer->name ?? 'غير محددة') . "\n\n"
            . "للموافقة أرسل: وافق طباعة {$printJob->id}\n"
            . "للرفض أرسل: رفض طباعة {$printJob->id}\n"
            . "لمعاينة الملف أولاً أرسل: ارسل لي الملف طباعة {$printJob->id}";

        $this->adminNotifier->notify($text, ['source' => 'print_approval_request', 'print_job_id' => $printJob->id]);
    }
}
