<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Services\CentralApiService;
use Illuminate\Support\Facades\Log;

class RetryFailedMessages extends Command
{
    protected $signature = 'messages:retry-failed {--limit=50 : Maximum number of messages to retry}';
    protected $description = 'إعادة إرسال الرسائل الفاشلة';

    public function handle()
    {
        $limit = $this->option('limit');

        $failedMessages = Message::where('status', 'failed')
            ->where('retry_count', '<', config('app.max_retry_attempts', 5))
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        if ($failedMessages->isEmpty()) {
            $this->info('لا توجد رسائل فاشلة لإعادة إرسالها');
            Log::info('No failed messages to retry');
            return Command::SUCCESS;
        }

        $retryCount = 0;
        $centralApiService = app(CentralApiService::class);

        $this->info("بدء إعادة إرسال {$failedMessages->count()} رسالة فاشلة...");

        foreach ($failedMessages as $message) {
            try {
                $this->line("إعادة إرسال الرسالة #{$message->id} إلى {$message->phone_number}");

                $result = $centralApiService->sendMessage($message);

                if ($result['success']) {
                    $message->updateStatus('sent', [
                        'central_message_id' => $result['message_id'],
                        'sent_at' => now(),
                        'error_message' => null,
                    ]);

                    $retryCount++;
                    $this->info("✓ تم إعادة إرسال الرسالة #{$message->id} بنجاح");
                    Log::info("Message #{$message->id} retried successfully");
                } else {
                    $message->incrementRetryCount();
                    $message->update(['error_message' => $result['error']]);

                    $this->warn("✗ فشل في إعادة إرسال الرسالة #{$message->id}: {$result['error']}");
                    Log::warning("Failed to retry message #{$message->id}: {$result['error']}");
                }

                // تأخير قصير بين الرسائل لتجنب rate limiting
                usleep(500000); // 0.5 ثانية

            } catch (\Exception $e) {
                $message->incrementRetryCount();
                $message->update(['error_message' => $e->getMessage()]);

                $this->error("خطأ في إعادة إرسال الرسالة #{$message->id}: {$e->getMessage()}");
                Log::error("Error retrying message #{$message->id}: {$e->getMessage()}");
            }
        }

        $this->info("تم إعادة إرسال {$retryCount} رسالة من أصل {$failedMessages->count()}");
        Log::info("Retry process completed: {$retryCount}/{$failedMessages->count()} messages retried successfully");

        return Command::SUCCESS;
    }
}
