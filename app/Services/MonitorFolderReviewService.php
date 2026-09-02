<?php

namespace App\Services;

use App\Jobs\SendMessageJob;
use App\Models\ExtractionCorrection;
use App\Models\Message;
use App\Models\Printer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * منطق الموافقة/الرفض على ملف محجوز بحالة review_pending من مجلد المراقبة (سواء بسبب استخراج رقم
 * جوال منخفض الثقة، أو بسبب تفعيل "موافقة قبل الإرسال" العامة لكل الملفات). مستخدَم من صفحة متابعة
 * الإرسال (زر في لوحة التحكم) ومن أمر واتساب نصي "وافق ارسال <رقم>"/"رفض ارسال <رقم>" من المسؤول،
 * لذا استُخرج هنا كخدمة مشتركة بدل تكرار منطق نقل الملفات بين مسارين.
 */
class MonitorFolderReviewService
{
    public function __construct(private AdminNotifier $adminNotifier)
    {
    }

    /**
     * يُستخدَم لملفات حُجزت بلا أي رقم جوال مستخرَج تلقائياً (metadata.needs_phone_entry، راجع
     * MonitorFolderCommand::holdForManualPhoneEntry) — المسؤول يُدخل الرقم يدوياً من صفحة متابعة
     * الإرسال، فيُحدَّث الرقم على الرسالة ثم تُعتمد نفس آلية approve() المعتادة للإرسال الفعلي.
     *
     * @return array{success: bool, message: string}
     */
    public function setPhoneAndApprove(Message $message, string $phoneNumber): array
    {
        if ($message->status !== 'review_pending') {
            return ['success' => false, 'message' => 'هذا الملف ليس بانتظار المراجعة حالياً.'];
        }

        $formatted = format_phone_number($phoneNumber);
        if (empty($formatted) || !preg_match('/^[0-9]{9,15}$/', $formatted)) {
            return ['success' => false, 'message' => 'رقم الجوال المُدخَل غير صالح.'];
        }

        $metadata = $message->metadata ?? [];
        unset($metadata['needs_phone_entry']);
        $message->update(['phone_number' => $formatted, 'metadata' => $metadata ?: null]);

        return $this->approve($message);
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function approve(Message $message): array
    {
        if ($message->status !== 'review_pending') {
            return ['success' => false, 'message' => 'هذا الملف ليس بانتظار المراجعة حالياً.'];
        }

        $folderPath = config('app.monitor_folder_path', 'C:/PrintMonitor');
        $reviewFile = $this->locatePhysicalFile($folderPath . '/review', $message);

        if (!$reviewFile) {
            return ['success' => false, 'message' => 'تعذّر العثور على الملف الفعلي في مجلد المراجعة.'];
        }

        // [New] إن كان الملف قد عُلِّق بسبب قاعدة hold_for_approval (لا بسبب رقم غير مؤكد)، نُنفِّذ
        // بالضبط الإجراء الذي كانت القاعدة ستطبّقه أصلاً (طباعة/إرسال/كليهما) بدل افتراض الإرسال
        // دائماً — وإلا سنتجاهل نيّة القاعدة الأصلية (مثال: قاعدة "طباعة فقط بانتظار موافقة").
        $pendingAction = $message->metadata['pending_action'] ?? 'print_and_send';
        $pendingPrinterId = $message->metadata['pending_printer_id'] ?? null;

        // save_only/print_only لا تمر بـSendMessageJob (الذي يؤرشف الملف لاحقاً) فتذهب مباشرة لمجلد
        // saved/ المستقل عن processing، بنفس منطق MonitorFolderCommand.
        $isTerminalWithoutSend = in_array($pendingAction, ['save_only', 'print_only'], true);
        $destinationPath = $isTerminalWithoutSend ? ($folderPath . '/saved') : ($folderPath . '/processing');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        try {
            $targetPath = $destinationPath . '/' . basename($reviewFile);
            if (File::exists($targetPath)) {
                File::delete($reviewFile);
            } else {
                File::move($reviewFile, $targetPath);
            }

            $newStatus = in_array($pendingAction, ['print_and_send', 'send_only'], true) ? 'pending' : 'skipped_send';
            $message->update(['status' => $newStatus]);

            if (in_array($pendingAction, ['print_and_send', 'send_only'], true)) {
                dispatch(new SendMessageJob($message->id));
            }

            if (in_array($pendingAction, ['print_and_send', 'print_only'], true)) {
                $printer = $pendingPrinterId ? Printer::find($pendingPrinterId) : null;
                if ($printer && $printer->is_active) {
                    app(\App\Services\PrintJobDispatcher::class)->dispatch($message, $printer, 'monitor_folder');
                }
            }

            $this->recordCorrection($message, 'approved');

            Log::info('MonitorFolder: manual review approved', [
                'message_id' => $message->id,
                'phone' => $message->phone_number,
                'pending_action' => $pendingAction,
            ]);

            $actionLabel = match ($pendingAction) {
                'print_only' => 'سيُطبع الملف الآن دون إرسال.',
                'send_only', 'print_and_send' => "سيُرسل الملف إلى {$message->phone_number} الآن.",
                default => 'تم حفظ الملف دون إرسال أو طباعة.',
            };

            return ['success' => true, 'message' => "تمت الموافقة. {$actionLabel}"];
        } catch (\Exception $e) {
            Log::error('MonitorFolder: failed to approve reviewed file: ' . $e->getMessage());
            return ['success' => false, 'message' => 'فشلت الموافقة: ' . $e->getMessage()];
        }
    }

    /**
     * @return array{success: bool, message: string}
     */
    public function reject(Message $message, ?string $actorLabel = null): array
    {
        if ($message->status !== 'review_pending') {
            return ['success' => false, 'message' => 'هذا الملف ليس بانتظار المراجعة حالياً.'];
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
                'error_message' => 'تم الرفض يدوياً من قبل ' . ($actorLabel ?: 'مستخدم') . '.',
            ]);

            $this->recordCorrection($message, 'rejected');

            Log::info('MonitorFolder: manual review rejected', ['message_id' => $message->id, 'phone' => $message->phone_number]);

            return ['success' => true, 'message' => 'تم رفض الملف ونقله لمجلد "فشلت".'];
        } catch (\Exception $e) {
            Log::error('MonitorFolder: failed to reject reviewed file: ' . $e->getMessage());
            return ['success' => false, 'message' => 'فشل الرفض: ' . $e->getMessage()];
        }
    }

    /**
     * الموافقة على كل الملفات الحالية بانتظار المراجعة دفعة واحدة (زر "موافقة على الكل" أو أمر
     * واتساب "وافق الكل ارسال"). يُعيد عدد الملفات التي تمت الموافقة عليها فعلياً.
     */
    public function approveAllPending(): int
    {
        $count = 0;
        foreach (Message::where('status', 'review_pending')->get() as $message) {
            if ($this->approve($message)['success']) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * إبلاغ المسؤول (أو المسؤولين) عبر واتساب بملف بانتظار موافقته قبل إرساله، مع تعليمات الرد
     * النصي البسيط للموافقة/الرفض. عامة (public) لإعادة استخدامها من أمر التذكير التلقائي
     * (SendApprovalReminders، كل الرسائل بحالة review_pending تشمل هذه أيضاً) — لذا مركزية هنا بدل
     * تكرارها في MonitorFolderCommand، حتى لا يختلف نص التذكير عن نص الإشعار الأول كما حدث سابقاً.
     * $customNotice نص تحذير إضافي اختياري يُدرَج أعلى الرسالة (يُستخدم حالياً لتنبيه اشتباه التكرار).
     */
    public function notifyAdminForApproval(Message $message, bool $isReminder = false, ?string $customNotice = null): void
    {
        $prefix = $isReminder ? "⏰ تذكير — لا يزال بانتظار موافقتك:\n\n" : '';
        $notice = $customNotice ? $customNotice . "\n\n" : '';
        $link = rtrim(config('app.url'), '/') . '/print-monitor';

        // ملفات بلا أي رقم مستخرَج تلقائياً (راجع MonitorFolderCommand::holdForManualPhoneEntry):
        // لا يوجد رقم جوال لعرضه، ولا معنى لأوامر "وافق/رفض ارسال" النصية لأن الموافقة تتطلب أولاً
        // إدخال الرقم من الصفحة نفسها — رسالة مختلفة تماماً بدل القالب العام.
        if ($message->metadata['needs_phone_entry'] ?? false) {
            $text = $prefix . $notice . "📎 ملف بانتظار إدخال رقم الجوال يدوياً\n"
                . "رقم الرسالة: {$message->id}\n"
                . "الملف: {$message->file_name}\n\n"
                . "لم يتمكن النظام من استخراج رقم جوال تلقائياً. أدخل الرقم من صفحة متابعة الإرسال لإتمام الإرسال:\n"
                . $link;

            $this->adminNotifier->notify($text, ['source' => 'monitor_needs_phone_entry', 'reviewed_message_id' => $message->id]);
            return;
        }

        $text = $prefix . $notice . "📨 ملف بانتظار موافقتك قبل الإرسال عبر واتساب\n"
            . "رقم الرسالة: {$message->id}\n"
            . "إلى: {$message->phone_number}\n"
            . "الملف: {$message->file_name}\n\n"
            . "للموافقة أرسل: وافق ارسال {$message->id}\n"
            . "للرفض أرسل: رفض ارسال {$message->id}\n"
            . "لمعاينة الملف أولاً أرسل: ارسل لي الملف ارسال {$message->id}\n"
            . "أو راجع مباشرة: {$link}";

        $this->adminNotifier->notify($text, ['source' => 'monitor_send_approval_request', 'reviewed_message_id' => $message->id]);
    }

    /**
     * تسجيل قرار المراجعة اليدوية (موافقة/رفض) كتصحيح، فقط للرسائل التي حُجزت فعلياً بسبب استخراج
     * منخفض الثقة (وليس بسبب "موافقة إلزامية لكل الملفات" العامة التي لا علاقة لها بمستوى الثقة —
     * راجع MonitorFolderCommand::$globalApprovalRequired). المصدر يُقرأ من metadata['review_source']
     * الذي حفظه MonitorFolderCommand::holdForManualReview وقت الحجز.
     */
    private function recordCorrection(Message $message, string $decision): void
    {
        $source = $message->metadata['review_source'] ?? null;
        if (!$source) {
            return;
        }

        try {
            ExtractionCorrection::create([
                'phone_number' => $message->phone_number,
                'source' => $source,
                'decision' => $decision,
                'message_id' => $message->id,
                'source_filename' => $message->source_filename,
            ]);
        } catch (\Exception $e) {
            Log::warning('MonitorFolderReviewService: failed to record extraction correction: ' . $e->getMessage());
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
}
