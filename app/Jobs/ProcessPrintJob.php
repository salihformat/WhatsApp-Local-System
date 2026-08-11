<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\PrintJob;
use App\Services\FileTypeResolver;
use App\Services\OfficeToPdfConverter;
use App\Services\PrintFolderManager;
use App\Services\PrintService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser as PdfParser;
use Throwable;

class ProcessPrintJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $printJobId;

    public $tries = 3;
    // [Fix 2026-08-06] يجب أن تتجاوز مجموع مهلة تحويل ملفات الأوفيس (office_conversion_timeout)
    // ومهلة الطباعة الداخلية (print_timeout) بهامش أمان، وإلا يقتل Laravel المهمة قبل أن تحصل
    // العملية الفعلية (LibreOffice أو SumatraPDF) على فرصة إنهاء عملها أو رفع استثناء واضح.
    public $timeout = 340;
    public $backoff = [30, 60, 120];

    public function __construct($printJobId)
    {
        $this->printJobId = $printJobId;
    }

    public function handle(PrintService $printService, OfficeToPdfConverter $officeConverter): void
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
            $this->ensureLocalFile($job, $officeConverter);
            $printService->print($job);

            $pages = $this->countPages($job->file_path);

            $job->update(['status' => 'completed', 'printed_at' => now(), 'error_message' => null, 'pages' => $pages]);

            if ($pages && $job->printer) {
                $job->printer->increment('pages_printed', $pages);
            }

            Log::info("Print job {$job->id} completed successfully", [
                'printer' => $job->printer?->name,
                'file' => $job->file_name,
                'pages' => $pages,
            ]);

            $this->notifySender($job, true);
            app(PrintFolderManager::class)->moveToArchive($job);
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
        app(PrintFolderManager::class)->moveToFailed($job);
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

        $customerPhone = $job->message->phone_number ?? 'غير معروف (مصدره مجلد الطباعة المحلي)';
        $text = "⚠️ فشلت طباعة طلب من العميل {$customerPhone}\n"
            . "الملف: {$job->file_name}\n"
            . "الطابعة: " . ($job->printer->name ?? 'غير محددة') . "\n"
            . "الخطأ: {$rawError}";

        app(\App\Services\AdminNotifier::class)->notify($text, ['source' => 'print_job_failure_alert', 'print_job_id' => $job->id]);
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
     * الملفات الواردة عبر واتساب تُخزَّن كرابط بعيد من النظام المركزي (media_url) أو كمسار عام نسبي
     * (/storage/...، لملفات مجلد المراقبة القديم)، ويجب إحضارها محلياً كنسخة عمل خاصة بنا أولاً لأن
     * أدوات الطباعة (SumatraPDF) تتطلب مساراً محلياً فعلياً. [Fix 2026-08-10] ملفات "مجلد الطباعة"
     * الجديد (print_folder) تصل بمسار محلي فعلي جاهز من البداية (source_file_path) — سابقاً كان أي
     * مسار محلي موجود فعلياً على القرص يُعتبر "جاهزاً" ويتخطى هذه الدالة بالكامل، فيتفادى تحويل
     * ملفات الأوفيس وتحجيم الصور القياسي بالخطأ. الحل: نأخذ نسخة عمل مؤقتة دوماً (سواء التنزيل من
     * رابط بعيد أو نسخ ملف محلي جاهز)، ونُطبّق خطوات التحويل/التحجيم عليها بشكل موحّد بلا استثناء —
     * كما يحمي الملف الأصلي المؤرشف في مجلد الطباعة من التعديل المباشر (تحجيم الصور يُعدّل في مكانه).
     */
    private function ensureLocalFile(PrintJob $job, OfficeToPdfConverter $officeConverter): void
    {
        $sourcePath = $this->resolveExistingLocalPath($job->file_path);

        if ($sourcePath !== null) {
            $extension = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION))
                ?: FileTypeResolver::resolveExtension($job->file_name, $job->file_path, $job->file_type, 'pdf');
            $safeFileName = 'print_job_' . $job->id . '.' . $extension;
            $relativePath = trim(config('printing.download_path'), '/') . '/' . $safeFileName;
            Storage::disk('local')->put($relativePath, file_get_contents($sourcePath));
        } else {
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
            //
            // [Fix 2026-08-06] $job->file_name فارغ دائماً لصور واتساب، وكان يؤدي سابقاً لافتراض ".pdf"
            // دوماً حتى للصور الحقيقية — فيُحفظ محتوى JPEG ثنائي باسم ملف ".pdf" فيفشل عند فتحه لاحقاً.
            // FileTypeResolver يلجأ للرابط البعيد الأصلي (قبل استبداله أدناه بالمسار المحلي) ثم لنوع
            // MIME (file_type) قبل الرجوع أخيراً لـ'pdf' كافتراض.
            $extension = FileTypeResolver::resolveExtension($job->file_name, $job->file_path, $job->file_type, 'pdf');
            $safeFileName = 'print_job_' . $job->id . '.' . $extension;

            $relativePath = trim(config('printing.download_path'), '/') . '/' . $safeFileName;
            Storage::disk('local')->put($relativePath, $response->body());
        }

        $localPath = Storage::disk('local')->path($relativePath);

        // [Fix 2026-08-06] SumatraPDF لا يدعم ملفات Word/Excel/PowerPoint مباشرة (بعكس PDF والصور)
        // — تُحوَّل أولاً إلى PDF عبر LibreOffice (headless)، ثم يُستكمل بقية المسار كأي PDF عادي.
        // اسم ملف الإخراج من LibreOffice مطابق دائماً لاسم الملف المُدخَل بامتداد .pdf (نفس مجلد
        // print_jobs)، لذا يبقى نمط التسمية print_job_{id}.pdf متسقاً بعد التحويل.
        if (in_array($extension, config('printing.office_extensions', []), true)) {
            $convertedPath = $officeConverter->convert($localPath);
            Storage::disk('local')->delete($relativePath);
            $localPath = $convertedPath;
            $extension = 'pdf';
        }

        // [Fix 2026-08-06] لوحظ فعلياً (فحص حي) أن صور واتساب/S3 تصل بلا أي بيانات دقة (DPI) —
        // فحص JFIF أظهر density=1x1 (بلا وحدة قياس مطلقة). المحاولة الأولى (تعيين DPI فقط عبر
        // imageresolution) حلّت مشكلة عدم ظهور المحتوى، لكن كشفت مشكلة تالية: SumatraPDF يشتق حجم
        // "صفحة" الطباعة مباشرة من أبعاد الصورة نفسها (بكسل ÷ DPI)، فينتج مقاس صفحة غير قياسي
        // (custom) لا يطابق أي درج ورق مُعرَّف في الطابعة — فيظهر خطأ "الورق غير موجود" رغم وجود
        // ورق A4/Letter فعلياً بالطابعة. الحل الجذري: وضع الصورة داخل صفحة كاملة بمقاس قياسي فعلي
        // (بخلفية بيضاء، مع تصغير الصورة إن لزم لتناسب هامش الصفحة) بدل الاكتفاء بتعيين DPI فقط.
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            $this->fitImageToStandardPage($localPath, $extension);
        }

        $job->update(['file_path' => $localPath]);
    }

    /**
     * عدد صفحات الملف النهائي الجاهز للطباعة (بعد أي تحويل أوفيس/تحجيم صورة) — تقدير تقريبي لتخطيط
     * استهلاك الحبر/الورق (راجع Printer::pages_printed)، وليس عداداً رسمياً دقيقاً. الصور دوماً صفحة
     * واحدة (تُوضع مسبقاً داخل صفحة قياسية كاملة عبر fitImageToStandardPage). فشل حساب صفحات PDF
     * (ملف تالف مثلاً) لا يُفشل المهمة نفسها — الطباعة نجحت فعلاً، فقط لا يُحتسب لها عدد صفحات.
     */
    private function countPages(string $localPath): ?int
    {
        $extension = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'], true)) {
            return 1;
        }

        if ($extension !== 'pdf') {
            return null;
        }

        try {
            return count((new PdfParser())->parseFile($localPath)->getPages());
        } catch (Throwable $e) {
            Log::warning("ProcessPrintJob: failed to count PDF pages for '{$localPath}'", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * يحدد المسار المحلي الفعلي للملف إن كان أصلاً على القرص (بلا حاجة لتنزيله من رابط بعيد):
     * إما مساراً مطلقاً موجوداً فعلاً (ملفات مجلد الطباعة الجديد)، أو مساراً عاماً نسبياً بصيغة
     * "/storage/..." (ملفات مجلد المراقبة المُرسَلة عبر واتساب، file_path على Message مُخزَّن بهذه
     * الصيغة لبناء رابط عام لا كمسار قرص). يُعيد null إن لم يكن الملف موجوداً محلياً بأي من الشكلين.
     */
    private function resolveExistingLocalPath(string $filePath): ?string
    {
        if (file_exists($filePath)) {
            return $filePath;
        }

        if (str_starts_with($filePath, '/storage/')) {
            $relative = ltrim(substr($filePath, strlen('/storage/')), '/');
            $publicPath = Storage::disk('public')->path($relative);

            if (file_exists($publicPath)) {
                return $publicPath;
            }
        }

        return null;
    }

    /**
     * يضع الصورة داخل صفحة قياسية كاملة (A4/Letter افتراضياً حسب printing.image_page_size) بدقة
     * printing.image_dpi، مع تصغيرها (بلا تكبير) لتناسب هامش الصفحة مع الحفاظ على أبعادها الأصلية —
     * انظر الشرح في ensureLocalFile أعلاه. فشل GD بصمت (صورة تالفة مثلاً) لا يجب أن يمنع محاولة
     * الطباعة بالملف الأصلي كما وصل.
     */
    private function fitImageToStandardPage(string $path, string $extension): void
    {
        try {
            $source = @imagecreatefromstring(file_get_contents($path));
            if (!$source) {
                return;
            }

            $dpi = (int) config('printing.image_dpi', 200);
            $pageSizeMm = strtolower(config('printing.image_page_size', 'a4')) === 'letter'
                ? [215.9, 279.4]
                : [210.0, 297.0]; // A4

            $pageWidthPx = (int) round($pageSizeMm[0] / 25.4 * $dpi);
            $pageHeightPx = (int) round($pageSizeMm[1] / 25.4 * $dpi);

            $marginPx = (int) round(10 / 25.4 * $dpi); // هامش 10مم على كل جانب
            $maxContentWidth = $pageWidthPx - (2 * $marginPx);
            $maxContentHeight = $pageHeightPx - (2 * $marginPx);

            $sourceWidth = imagesx($source);
            $sourceHeight = imagesy($source);

            // تصغير فقط عند الحاجة (بلا تكبير) — الحفاظ على نسبة الأبعاد الأصلية
            $scale = min($maxContentWidth / $sourceWidth, $maxContentHeight / $sourceHeight, 1.0);
            $targetWidth = (int) round($sourceWidth * $scale);
            $targetHeight = (int) round($sourceHeight * $scale);

            $page = imagecreatetruecolor($pageWidthPx, $pageHeightPx);
            $white = imagecolorallocate($page, 255, 255, 255);
            imagefill($page, 0, 0, $white);

            $destX = (int) round(($pageWidthPx - $targetWidth) / 2);
            $destY = (int) round(($pageHeightPx - $targetHeight) / 2);

            imagecopyresampled($page, $source, $destX, $destY, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);
            imagedestroy($source);

            imageresolution($page, $dpi, $dpi);

            match ($extension) {
                'png' => imagepng($page, $path),
                'gif' => imagegif($page, $path),
                default => imagejpeg($page, $path, 92),
            };

            imagedestroy($page);
        } catch (Throwable $e) {
            Log::warning('Failed to fit image to standard page before printing', ['path' => $path, 'error' => $e->getMessage()]);
        }
    }
}
