<?php

namespace App\Console\Commands;

use App\Models\Message;
use App\Models\PrintJob;
use App\Services\MonitorFolderReviewService;
use App\Services\PrintJobDispatcher;
use Illuminate\Console\Command;

/**
 * يُذكّر المسؤول تلقائياً عبر واتساب بأي طلب موافقة (طباعة أو إرسال) لا يزال معلّقاً بعد مرور
 * printing.approval_reminder_after_minutes دقيقة بلا رد، ثم يكرر التذكير كل
 * printing.approval_reminder_repeat_minutes دقيقة ما دام لا يزال معلّقاً — لتفادي ضياع طلب لساعات
 * لمجرد أن المسؤول لم ينتبه لإشعار واتساب الأول.
 */
class SendApprovalReminders extends Command
{
    protected $signature = 'printing:send-approval-reminders';

    protected $description = 'Send WhatsApp reminders for print/send approval requests still pending after a configurable delay';

    public function handle(PrintJobDispatcher $printJobDispatcher, MonitorFolderReviewService $reviewService): int
    {
        $repeatMinutes = (int) config('printing.approval_reminder_repeat_minutes', 30);
        if ($repeatMinutes <= 0) {
            $this->info('Approval reminders disabled (printing.approval_reminder_repeat_minutes = 0).');
            return self::SUCCESS;
        }

        $afterMinutes = (int) config('printing.approval_reminder_after_minutes', 20);

        $printJobsCount = 0;
        foreach ($this->duePrintJobs($afterMinutes, $repeatMinutes) as $printJob) {
            $printJobDispatcher->notifyAdminForApproval($printJob, isReminder: true);
            $printJob->update(['reminder_sent_at' => now()]);
            $printJobsCount++;
        }

        $messagesCount = 0;
        foreach ($this->dueReviewMessages($afterMinutes, $repeatMinutes) as $message) {
            $reviewService->notifyAdminForApproval($message, isReminder: true);
            $message->update(['reminder_sent_at' => now()]);
            $messagesCount++;
        }

        $this->info("Sent {$printJobsCount} print-approval reminder(s) and {$messagesCount} send-approval reminder(s).");

        return self::SUCCESS;
    }

    private function duePrintJobs(int $afterMinutes, int $repeatMinutes)
    {
        return PrintJob::with(['printer', 'message'])
            ->where('status', 'awaiting_approval')
            ->where('created_at', '<=', now()->subMinutes($afterMinutes))
            ->where(function ($query) use ($repeatMinutes) {
                $query->whereNull('reminder_sent_at')
                    ->orWhere('reminder_sent_at', '<=', now()->subMinutes($repeatMinutes));
            })
            ->get();
    }

    private function dueReviewMessages(int $afterMinutes, int $repeatMinutes)
    {
        return Message::where('status', 'review_pending')
            ->where('created_at', '<=', now()->subMinutes($afterMinutes))
            ->where(function ($query) use ($repeatMinutes) {
                $query->whereNull('reminder_sent_at')
                    ->orWhere('reminder_sent_at', '<=', now()->subMinutes($repeatMinutes));
            })
            ->get();
    }
}
