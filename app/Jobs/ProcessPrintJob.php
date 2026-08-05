<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\PrintJob;
use App\Services\PrintService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessPrintJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $printJobId;

    public $tries = 3;
    // يجب أن تتجاوز مهلة الطباعة الداخلية (printing.print_timeout) بهامش أمان، وإلا يقتل Laravel
    // المهمة قبل أن تحصل عملية SumatraPDF نفسها على فرصة إنهاء الطباعة أو رفع استثناء واضح
    public $timeout = 220;
    public $backoff = [30, 60, 120];

    public function __construct($printJobId)
    {
        $this->printJobId = $printJobId;
    }

    public function handle(PrintService $printService): void
    {
        $job = PrintJob::find($this->printJobId);

        if (!$job) {
            Log::error("PrintJob not found for ID: {$this->printJobId}");
            return;
        }

        if (!in_array($job->status, ['pending', 'failed'])) {
            Log::info("PrintJob {$job->id} already processed with status: {$job->status}");
            return;
        }

        $job->update(['status' => 'printing', 'attempts' => $job->attempts + 1]);

        try {
            $this->ensureLocalFile($job);
            $printService->print($job);

            $job->update(['status' => 'completed', 'printed_at' => now(), 'error_message' => null]);

            Log::info("Print job {$job->id} completed successfully", [
                'printer' => $job->printer?->name,
                'file' => $job->file_name,
            ]);

            $this->notifySender($job, true);
        } catch (Throwable $e) {
            Log::error("Print job {$job->id} failed", ['error' => $e->getMessage()]);

            $job->update(['status' => 'failed', 'error_message' => $e->getMessage()]);

            // لا نُبلّغ المُرسِل بالفشل هنا — هذه قد تكون إحدى محاولتين متبقيتين (tries=3، backoff
            // [30, 60, 120]) وقد تنجح الطباعة تلقائياً عند إعادة المحاولة. إبلاغه في كل محاولة فاشلة
            // يُزعجه برسائل متكررة قد تتراجع عنها المحاولة التالية بنجاح. الإبلاغ الفعلي فقط عند
            // الفشل النهائي في failed() أدناه بعد استنفاد كل المحاولات.
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        $job = PrintJob::find($this->printJobId);

        // لا نُرجع مهمة نجحت بالفعل (عبر Dispatch مكرر آخر لنفس المهمة) إلى "فشلت"
        if (!$job || $job->status === 'completed') {
            return;
        }

        $job->update([
            'status' => 'failed',
            'error_message' => "فشل نهائي بعد {$this->tries} محاولات: " . $exception->getMessage(),
        ]);

        Log::error("PrintJob {$this->printJobId} failed permanently", ['error' => $exception->getMessage()]);

        $this->notifySender($job, false);
        $this->notifyOwner($job, $exception->getMessage());
    }

    /**
     * إبلاغ من طلب الطباعة عبر واتساب (صاحب الرسالة الواردة المرتبطة بهذه المهمة) بنتيجة طلبه —
     * نجحت الطباعة أم فشلت، وفي حال الفشل بسبب مبسَّط لا يفترض معرفة تقنية (بخلاف التنبيه الفني
     * الكامل الذي يصل لصاحب المنشأة عبر notifyOwner). يُمكن تعطيله بالكامل عبر إعداد في .env.
     */
    private function notifySender(PrintJob $job, bool $success): void
    {
        if (!config('printing.reply_status_to_sender')) {
            return;
        }

        $sourceMessage = $job->message;
        if (!$sourceMessage || empty($sourceMessage->phone_number)) {
            return;
        }

        // [Fix 2026-08-05] تأكيد "تمت الطباعة بنجاح" غير موثوق لطابعات لا تدعم إبلاغاً حقيقياً عن
        // حالتها (راجع الشرح في PrintService::print) — حالة "completed" لهذه الطابعات تعني فقط أن
        // SumatraPDF أنهى الأمر بلا خطأ برمجي، وليست تأكيداً أن الورقة خرجت فعلياً. لتفادي تأكيد كاذب
        // للعميل، لا يصله سوى رسالة الاستلام الأولى (من MessageController) بلا أي تأكيد لاحق بالنجاح.
        // الفشل (عند $success=false) يبقى يُبلَّغ دائماً بصرف النظر عن هذا الإعداد، لأنه في هذه الحالة
        // ناتج عن خطأ برمجي حقيقي (تنزيل/معالجة/SumatraPDF) وليس فحص صحة طابعة غير موثوق.
        if ($success && !($job->printer?->supports_status_check)) {
            Log::info("PrintJob {$job->id}: skipped success confirmation — printer does not support reliable status checking", [
                'printer' => $job->printer?->name,
            ]);
            return;
        }

        $text = $success
            ? "✅ تم طباعة ملفك \"{$job->file_name}\" بنجاح" . ($job->printer ? " على الطابعة \"{$job->printer->name}\"." : '.')
            : "❌ عذراً، تعذّرت طباعة ملفك \"{$job->file_name}\".\nالسبب: " . $this->friendlyFailureReason($job->error_message ?? '') . "\nسيتم متابعة الأمر من قبلنا.";

        try {
            $reply = Message::create([
                'conversation_id' => $sourceMessage->conversation_id,
                'phone_number' => $sourceMessage->phone_number,
                'message_text' => $text,
                'message_type' => 'text',
                'status' => 'pending',
                'metadata' => ['source' => 'print_status_reply', 'print_job_id' => $job->id],
            ]);

            dispatch(new SendMessageJob($reply->id));

            Log::info("PrintJob {$job->id}: status reply dispatched to sender", [
                'phone' => $sourceMessage->phone_number,
                'success' => $success,
            ]);
        } catch (Throwable $e) {
            Log::error("PrintJob {$job->id}: failed to dispatch status reply to sender", ['error' => $e->getMessage()]);
        }
    }

    /**
     * تنبيه فني منفصل لصاحب المنشأة (رقم printing.alert_phone_number، نفس رقم تنبيهات صحة الطابعة)
     * بالخطأ الحقيقي غير المبسَّط عند فشل طلب طباعة نهائياً — لمتابعته فنياً، بخلاف الرسالة المبسَّطة
     * التي يستلمها العميل نفسه عبر notifySender().
     */
    private function notifyOwner(PrintJob $job, string $rawError): void
    {
        if (!config('printing.notify_owner_on_job_failure')) {
            return;
        }

        $ownerPhone = config('printing.alert_phone_number');
        if (empty($ownerPhone)) {
            return;
        }

        $customerPhone = $job->message->phone_number ?? 'غير معروف';
        $text = "⚠️ فشلت طباعة طلب من العميل {$customerPhone}\n"
            . "الملف: {$job->file_name}\n"
            . "الطابعة: " . ($job->printer->name ?? 'غير محددة') . "\n"
            . "الخطأ: {$rawError}";

        try {
            $message = Message::create([
                'phone_number' => $ownerPhone,
                'message_text' => $text,
                'message_type' => 'text',
                'status' => 'pending',
            ]);

            dispatch(new SendMessageJob($message->id));
        } catch (Throwable $e) {
            Log::error("PrintJob {$job->id}: failed to dispatch owner failure alert", ['error' => $e->getMessage()]);
        }
    }

    /**
     * يحوّل رسائل الأخطاء التقنية (استثناءات PrintService/SumatraPDF/Symfony Process) إلى شرح
     * مبسّط يصلح لإرساله للعميل مباشرة، دون تفاصيل تقنية داخلية لا تعنيه.
     */
    private function friendlyFailureReason(string $rawError): string
    {
        // رسائل PrinterMonitorService (نفاد ورق/حبر/انحشار/عدم اتصال...) عربية ومباشرة أصلاً
        // (راجع PrinterMonitorService::ERROR_STATE_MAP) — تُمرَّر كما هي بلا تعميم، فهي بالفعل
        // أدق وأوضح للعميل من أي رسالة عامة نستبدلها بها.
        if (str_starts_with($rawError, 'الطابعة غير جاهزة حالياً:') || str_starts_with($rawError, 'تم إرسال أمر الطباعة لكن الطابعة أصبحت غير جاهزة:')) {
            return $rawError;
        }

        $patterns = [
            'فشل تنزيل الملف' => 'تعذّر الوصول إلى الملف المطلوب طباعته.',
            'الملف غير موجود محلياً' => 'تعذّر الوصول إلى الملف المطلوب طباعته.',
            'مسار الملف غير صالح' => 'الملف المطلوب طباعته غير صالح.',
            'نوع الملف' => 'صيغة الملف غير مدعومة للطباعة التلقائية حالياً.',
            'نوع الطابعة' => 'إعداد الطابعة يحتاج متابعة فنية.',
            'SumatraPDF غير موجود' => 'إعداد نظام الطباعة يحتاج متابعة فنية.',
            'لا توجد طابعة محددة' => 'لم يتم تحديد طابعة مناسبة لهذا الطلب.',
            'ميزة الطباعة الذكية غير مفعّلة' => 'ميزة الطباعة التلقائية غير مفعّلة حالياً.',
        ];

        foreach ($patterns as $needle => $friendly) {
            if (str_contains($rawError, $needle)) {
                return $friendly;
            }
        }

        if (stripos($rawError, 'timeout') !== false || stripos($rawError, 'timed out') !== false) {
            return 'استغرقت عملية الطباعة وقتاً أطول من المتوقع (قد تكون الطابعة متوقفة أو مشغولة).';
        }

        if (stripos($rawError, 'offline') !== false) {
            return 'الطابعة غير متصلة حالياً.';
        }

        return 'حدث خطأ فني غير متوقع أثناء الطباعة، وتتم متابعته من فريق الدعم.';
    }

    /**
     * الملفات الواردة عبر واتساب تُخزَّن كرابط بعيد من النظام المركزي (media_url)،
     * يجب تنزيلها محلياً أولاً لأن أدوات الطباعة (SumatraPDF) تتطلب مساراً محلياً فعلياً.
     */
    private function ensureLocalFile(PrintJob $job): void
    {
        if (file_exists($job->file_path)) {
            return;
        }

        if (!filter_var($job->file_path, FILTER_VALIDATE_URL)) {
            throw new \Exception("مسار الملف غير صالح ولا يمكن الوصول إليه: {$job->file_path}");
        }

        // decode_content=false يمنع Guzzle/cURL من محاولة فك أي ترميز مذكور في Content-Encoding
        // تلقائياً. بعض الروابط (مثل تخزين S3 لدى المركزي) تُرسل قيمة Content-Encoding غير قياسية
        // (لوحظ فعلياً القيمة "base64" رغم أن المحتوى بالفعل بيانات ثنائية خام غير مُرمَّزة)، فيفشل
        // cURL بخطأ "Unrecognized content encoding type" لأنه لا يعرف كيف يفك هذا الترميز — بينما
        // نحن أصلاً لا نحتاج أي فك ترميز، فقط البايتات كما هي.
        $response = Http::timeout(60)
            ->withOptions(['decode_content' => false])
            ->get($job->file_path);
        if (!$response->successful()) {
            throw new \Exception("فشل تنزيل الملف من الرابط البعيد (HTTP {$response->status()})");
        }

        // اسم ملف آمن بأحرف ASCII فقط (بدل استخدام اسم الملف الأصلي كما وصل من واتساب، والذي غالباً
        // ما يحتوي أحرفاً عربية). لوحظ فعلياً أن تمرير مسار بأحرف عربية لعملية SumatraPDF الخارجية
        // (عبر Symfony Process على Windows) يتسبب بتعليقها حتى انتهاء المهلة (60 ثانية) بصمت، على
        // الأرجح بسبب سوء تحويل ترميز سطر الأوامر لغير ASCII في هذه البيئة تحديداً.
        $extension = pathinfo($job->file_name, PATHINFO_EXTENSION) ?: 'pdf';
        $safeFileName = 'print_job_' . $job->id . '.' . $extension;

        $relativePath = trim(config('printing.download_path'), '/') . '/' . $safeFileName;
        Storage::disk('local')->put($relativePath, $response->body());

        $job->update(['file_path' => Storage::disk('local')->path($relativePath)]);
    }
}
