<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use App\Models\Message;
use App\Models\ExtractionTrace;
use App\Jobs\SendMessageJob;
use Illuminate\Support\Facades\Log;

/**
 * صفحة متابعة مجلد المراقبة C:\PrintMonitor: أي الملفات وصلت، أُرسلت بنجاح (archive)،
 * فشلت (failed)، أو لا تزال قيد الانتظار/المعالجة — عرض مباشر لحالة المجلدات الفعلية على القرص.
 */
class PrintMonitorController extends Controller
{
    public function index()
    {
        $folderPath = config('app.monitor_folder_path', 'C:/PrintMonitor');
        $folderExists = File::exists($folderPath);

        $folders = [
            'pending' => ['label' => 'قيد الانتظار (لم تُعالَج بعد)', 'path' => $folderPath, 'root_only' => true],
            'review' => ['label' => 'بانتظار المراجعة', 'path' => $folderPath . '/review'],
            'processing' => ['label' => 'قيد المعالجة الآن', 'path' => $folderPath . '/processing'],
            'archive' => ['label' => 'أُرسلت بنجاح', 'path' => $folderPath . '/archive'],
            'failed' => ['label' => 'فشلت', 'path' => $folderPath . '/failed'],
        ];

        $data = [];
        foreach ($folders as $key => $folder) {
            $data[$key] = [
                'label' => $folder['label'],
                'exists' => File::exists($folder['path']),
                'files' => $this->listFiles($folder['path'], $folder['root_only'] ?? false),
            ];
        }

        return view('print-monitor.index', [
            'folderPath' => $folderPath,
            'folderExists' => $folderExists,
            'folders' => $data,
        ]);
    }

    private function listFiles(string $path, bool $rootOnly = false): array
    {
        if (!File::exists($path)) {
            return [];
        }

        $files = $rootOnly ? File::files($path) : File::files($path);

        $list = [];
        foreach ($files as $file) {
            $filename = $file->getFilename();
            if (str_starts_with($filename, '.')) {
                continue;
            }

            $message = $this->findMessageForFile($filename);
            $trace = $this->findTraceForFile($filename);

            $list[] = [
                'name' => $filename,
                'size' => $this->formatSize($file->getSize()),
                'modified_at' => $file->getMTime(),
                'message_id' => $message->id ?? null,
                'phone_number' => $message->phone_number ?? null,
                'status' => $message->status ?? null,
                'error_message' => $message->error_message ?? null,
                'trace' => $trace ? [
                    'source' => $trace->source,
                    'source_label' => $this->traceSourceLabel($trace->source),
                    'matched_label' => $trace->matched_label,
                    'file_number' => $trace->file_number,
                    'contact_found' => $trace->contact_id !== null,
                    'excluded' => $trace->excluded ?? [],
                    'rtl_corrected' => (bool) $trace->rtl_corrected,
                    'pdf_ocr_used' => (bool) $trace->pdf_ocr_used,
                ] : null,
            ];
        }

        // الأحدث أولاً
        usort($list, fn ($a, $b) => $b['modified_at'] <=> $a['modified_at']);

        return array_slice($list, 0, 100);
    }

    /**
     * مطابقة اسم ملف فعلي على القرص مع سجل الرسالة المقابل له، للحصول على رقم الجوال المُرسَل إليه
     * وسبب الفشل إن وُجد. المطابقة تتم عبر source_filename (اسم الملف الأصلي وقت المسح) أولاً،
     * ثم file_name (الاسم بعد التنظيف) كخيار احتياطي للسجلات القديمة قبل إضافة العمود.
     */
    private function findMessageForFile(string $filename): ?Message
    {
        return Message::where('source_filename', $filename)
            ->orWhere('file_name', $filename)
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * جلب أحدث سجل تتبّع استخراج مطابق لاسم الملف، لعرض "لماذا اتخذ النظام هذا القرار" في الواجهة.
     */
    private function findTraceForFile(string $filename): ?ExtractionTrace
    {
        return ExtractionTrace::where('filename', $filename)
            ->orderByDesc('created_at')
            ->first();
    }

    private function traceSourceLabel(?string $source): string
    {
        return match ($source) {
            'filename' => 'رقم الجوال مُستخرج من اسم الملف',
            'label' => 'رقم الجوال مُستخرج من محتوى الملف (كلمة دلالة)',
            'file_number' => 'تم العثور على رقم ملف داخل المستند وتم البحث عنه في جهات الاتصال',
            'file_number_verified' => 'لم توجد تسمية دقيقة، فتم البحث عن أي رقم قرب كلمة "no"/"رقم" والتحقق منه مقابل جهات الاتصال مباشرة',
            'ocr_missing' => 'تعذّرت القراءة البصرية (OCR) — البرنامج غير مثبَّت',
            'ocr_error' => 'تعذّرت القراءة البصرية (OCR) لهذا الملف',
            'empty_image_text' => 'الصورة/الصفحة الممسوحة ضوئياً لا تحتوي نصاً يمكن قراءته',
            'unlabeled_fallback' => 'لم توجد كلمة دلالة، تم استخدام أول رقم يشبه جوالاً سعودياً في المحتوى',
            'corrupted_fallback' => 'طبقة النص تبدو تالفة (مشكلة ترميز عربي)، تم البحث عن رقم بلا تسمية فقط',
            'env_fallback' => 'تم استخدام رقم الجوال الاحتياطي من الإعدادات (MONITOR_FALLBACK_PHONE)',
            'parse_error' => 'تعذّرت قراءة محتوى الملف',
            'empty_text' => 'الملف لا يحتوي على طبقة نص قابلة للقراءة',
            'no_match_in_content' => 'لم يُعثر على أي رقم أو تسمية صالحة داخل المحتوى',
            'none' => 'لم تتم أي محاولة استخراج (لا اسم ملف ولا محتوى مطابق)',
            default => 'غير معروف',
        };
    }

    /**
     * الموافقة على ملف محجوز للمراجعة اليدوية: نقله من مجلد review إلى processing وإرساله فعلياً.
     */
    public function approve(Message $message)
    {
        if ($message->status !== 'review_pending') {
            return back()->with('error', 'هذا الملف ليس بانتظار المراجعة حالياً.');
        }

        $folderPath = config('app.monitor_folder_path', 'C:/PrintMonitor');
        $reviewFile = $this->locatePhysicalFile($folderPath . '/review', $message);

        if (!$reviewFile) {
            return back()->with('error', 'تعذّر العثور على الملف الفعلي في مجلد المراجعة.');
        }

        $processingPath = $folderPath . '/processing';
        if (!File::exists($processingPath)) {
            File::makeDirectory($processingPath, 0755, true);
        }

        try {
            $targetPath = $processingPath . '/' . basename($reviewFile);
            if (File::exists($targetPath)) {
                File::delete($reviewFile);
            } else {
                File::move($reviewFile, $targetPath);
            }

            $message->update(['status' => 'pending']);
            dispatch(new SendMessageJob($message->id));

            activity('print-monitor')
                ->causedBy(auth()->user())
                ->withProperties(['message_id' => $message->id, 'phone_number' => $message->phone_number, 'file_name' => $message->file_name])
                ->log('تمت الموافقة على إرسال ملف كان بانتظار المراجعة اليدوية');

            Log::info('PrintMonitor: manual review approved', ['message_id' => $message->id, 'phone' => $message->phone_number]);

            return back()->with('success', "تمت الموافقة، سيُرسل الملف إلى {$message->phone_number} الآن.");
        } catch (\Exception $e) {
            Log::error('PrintMonitor: failed to approve reviewed file: ' . $e->getMessage());
            return back()->with('error', 'فشلت الموافقة: ' . $e->getMessage());
        }
    }

    /**
     * رفض ملف محجوز للمراجعة اليدوية: نقله لمجلد failed وتحديث حالة الرسالة دون إرسالها.
     */
    public function reject(Message $message)
    {
        if ($message->status !== 'review_pending') {
            return back()->with('error', 'هذا الملف ليس بانتظار المراجعة حالياً.');
        }

        $folderPath = config('app.monitor_folder_path', 'C:/PrintMonitor');
        $reviewFile = $this->locatePhysicalFile($folderPath . '/review', $message);

        $failedPath = $folderPath . '/failed';
        if (!File::exists($failedPath)) {
            File::makeDirectory($failedPath, 0755, true);
        }

        try {
            if ($reviewFile) {
                $targetPath = $failedPath . '/' . basename($reviewFile);
                if (File::exists($targetPath)) {
                    File::delete($reviewFile);
                } else {
                    File::move($reviewFile, $targetPath);
                }
            }

            $message->update([
                'status' => 'failed',
                'error_message' => 'تم الرفض يدوياً من قبل ' . (auth()->user()->name ?? 'مستخدم') . ' من صفحة متابعة الإرسال.',
            ]);

            activity('print-monitor')
                ->causedBy(auth()->user())
                ->withProperties(['message_id' => $message->id, 'phone_number' => $message->phone_number, 'file_name' => $message->file_name])
                ->log('تم رفض ملف كان بانتظار المراجعة اليدوية');

            Log::info('PrintMonitor: manual review rejected', ['message_id' => $message->id, 'phone' => $message->phone_number]);

            return back()->with('success', 'تم رفض الملف ونقله لمجلد "فشلت".');
        } catch (\Exception $e) {
            Log::error('PrintMonitor: failed to reject reviewed file: ' . $e->getMessage());
            return back()->with('error', 'فشل الرفض: ' . $e->getMessage());
        }
    }

    /**
     * البحث عن الملف الفعلي المطابق لرسالة بانتظار المراجعة داخل مجلد معيّن، عبر source_filename أولاً
     * ثم file_name كخيار احتياطي (نفس أسلوب المطابقة المستخدم في SendMessageJob::moveFolderFile).
     */
    private function locatePhysicalFile(string $dir, Message $message): ?string
    {
        if (!File::exists($dir)) {
            return null;
        }

        foreach (File::files($dir) as $file) {
            $fn = $file->getFilename();
            if ($fn === $message->source_filename || $fn === $message->file_name) {
                return $file->getPathname();
            }
        }

        return null;
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }
}
