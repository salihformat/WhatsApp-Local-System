<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Models\Message;
use App\Models\ExtractionTrace;
use App\Services\MonitorFolderReviewService;
use App\Services\PrintMonitorFileMatcher;

/**
 * صفحة متابعة مجلد المراقبة C:\PrintMonitor: أي الملفات وصلت، أُرسلت بنجاح (archive)،
 * فشلت (failed)، أو لا تزال قيد الانتظار/المعالجة — عرض مباشر لحالة المجلدات الفعلية على القرص.
 */
class PrintMonitorController extends Controller
{
    public function __construct(private PrintMonitorFileMatcher $matcher)
    {
    }

    public function index()
    {
        $folderPath = config('app.monitor_folder_path', 'C:/PrintMonitor');
        $folderExists = File::exists($folderPath);

        $folders = [
            'pending' => ['label' => __('local_agent.folder_pending'), 'path' => $folderPath, 'root_only' => true],
            'review' => ['label' => __('local_agent.folder_review'), 'path' => $folderPath . '/review'],
            'processing' => ['label' => __('local_agent.folder_processing'), 'path' => $folderPath . '/processing'],
            'archive' => ['label' => __('local_agent.folder_sent'), 'path' => $folderPath . '/archive'],
            'failed' => ['label' => __('local_agent.folder_failed'), 'path' => $folderPath . '/failed'],
        ];

        $data = [];
        foreach ($folders as $key => $folder) {
            $data[$key] = [
                'label' => $folder['label'],
                'exists' => File::exists($folder['path']),
                'files' => $this->listFiles($folder['path'], $key, $folder['root_only'] ?? false),
            ];
        }

        return view('print-monitor.index', [
            'folderPath' => $folderPath,
            'folderExists' => $folderExists,
            'folders' => $data,
        ]);
    }

    private function listFiles(string $path, string $folderKey, bool $rootOnly = false): array
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

            $message = $this->matcher->findMessageForFile($filename, $folderKey);
            $trace = $this->findTraceForFile($filename);

            $list[] = [
                'name' => $filename,
                'size' => $this->formatSize($file->getSize()),
                'modified_at' => $file->getMTime(),
                'message_id' => $message->id ?? null,
                'phone_number' => $message?->phone_number ?: null,
                'status' => $message->status ?? null,
                'error_message' => $message->error_message ?? null,
                'needs_phone_entry' => (bool) ($message->metadata['needs_phone_entry'] ?? false),
                'trace' => $trace ? [
                    'source' => $trace->source,
                    'source_label' => $this->traceSourceLabel($trace->source),
                    'matched_label' => $trace->matched_label,
                    'file_number' => $trace->file_number,
                    'contact_found' => $trace->contact_id !== null,
                    'excluded' => $trace->excluded ?? [],
                    'rtl_corrected' => (bool) $trace->rtl_corrected,
                    'pdf_ocr_used' => (bool) $trace->pdf_ocr_used,
                    'learned_trusted' => (bool) $trace->learned_trusted,
                ] : null,
            ];
        }

        // الأحدث أولاً
        usort($list, fn ($a, $b) => $b['modified_at'] <=> $a['modified_at']);

        return array_slice($list, 0, 100);
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
            'filename' => __('local_agent.trace_filename'),
            'label' => __('local_agent.trace_label'),
            'file_number' => __('local_agent.trace_file_number'),
            'file_number_verified' => __('local_agent.trace_file_number_verified'),
            'ocr_missing' => __('local_agent.trace_ocr_missing'),
            'ocr_error' => __('local_agent.trace_ocr_error'),
            'empty_image_text' => __('local_agent.trace_empty_image_text'),
            'unlabeled_fallback' => __('local_agent.trace_unlabeled_fallback'),
            'corrupted_fallback' => __('local_agent.trace_corrupted_fallback'),
            'env_fallback' => __('local_agent.trace_env_fallback'),
            'parse_error' => __('local_agent.trace_parse_error'),
            'empty_text' => __('local_agent.trace_empty_text'),
            'no_match_in_content' => __('local_agent.trace_no_match_in_content'),
            'none' => __('local_agent.trace_none'),
            default => __('local_agent.trace_unknown'),
        };
    }

    /**
     * الموافقة على ملف محجوز للمراجعة اليدوية: نقله من مجلد review إلى processing وإرساله فعلياً.
     */
    public function approve(Message $message, MonitorFolderReviewService $review)
    {
        $result = $review->approve($message);

        if ($result['success']) {
            activity('print-monitor')
                ->causedBy(auth()->user())
                ->withProperties(['message_id' => $message->id, 'phone_number' => $message->phone_number, 'file_name' => $message->file_name])
                ->log('تمت الموافقة على إرسال ملف كان بانتظار المراجعة اليدوية');
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * رفض ملف محجوز للمراجعة اليدوية: نقله لمجلد failed وتحديث حالة الرسالة دون إرسالها.
     */
    public function reject(Message $message, MonitorFolderReviewService $review)
    {
        $result = $review->reject($message, (auth()->user()->name ?? 'مستخدم') . ' من صفحة متابعة الإرسال');

        if ($result['success']) {
            activity('print-monitor')
                ->causedBy(auth()->user())
                ->withProperties(['message_id' => $message->id, 'phone_number' => $message->phone_number, 'file_name' => $message->file_name])
                ->log('تم رفض ملف كان بانتظار المراجعة اليدوية');
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * الموافقة على كل الملفات الحالية بانتظار المراجعة دفعة واحدة.
     */
    public function approveAll(MonitorFolderReviewService $review)
    {
        $count = $review->approveAllPending();

        if ($count > 0) {
            activity('print-monitor')
                ->causedBy(auth()->user())
                ->withProperties(['count' => $count])
                ->log("تمت الموافقة الجماعية على {$count} ملف كانت بانتظار المراجعة اليدوية");
        }

        return back()->with($count > 0 ? 'success' : 'info', $count > 0
            ? "تمت الموافقة على {$count} ملف."
            : 'لا توجد ملفات بانتظار المراجعة حالياً.');
    }

    /**
     * إدخال رقم جوال يدوياً لملف حُجز بلا أي رقم مستخرَج تلقائياً (وضع PRINT_EXTRACTION_METHOD=popup
     * عند فشل الاستخراج) ثم إرساله فوراً — بديل صفحة الويب لنافذة النظام المنبثقة غير القابلة للعمل.
     */
    public function setPhoneAndApprove(Message $message, Request $request, MonitorFolderReviewService $review)
    {
        $validated = $request->validate([
            'phone_number' => ['required', 'string', 'max:20'],
        ]);

        $result = $review->setPhoneAndApprove($message, $validated['phone_number']);

        if ($result['success']) {
            activity('print-monitor')
                ->causedBy(auth()->user())
                ->withProperties(['message_id' => $message->id, 'phone_number' => $message->phone_number, 'file_name' => $message->file_name])
                ->log('تم إدخال رقم الجوال يدوياً والموافقة على إرسال ملف');
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }
        return round($bytes / 1024, 1) . ' KB';
    }
}
