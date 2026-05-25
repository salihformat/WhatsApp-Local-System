<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Services\CentralApiService;
use Illuminate\Support\Facades\Log;

class SyncMessageStatus extends Command
{
    protected $signature = 'messages:sync-status {--limit=100 : Maximum number of messages to sync}';
    protected $description = 'مزامنة حالات الرسائل مع النظام المركزي';

    public function handle()
    {
        $limit = $this->option('limit');

        $pendingMessages = Message::whereIn('status', ['pending', 'sent'])
            ->whereNotNull('central_message_id')
            ->where('created_at', '>=', now()->subHours(48))
            ->orderBy('updated_at', 'asc')
            ->limit($limit)
            ->get();

        if ($pendingMessages->isEmpty()) {
            $this->info('لا توجد رسائل تحتاج مزامنة');
            return Command::SUCCESS;
        }

        $syncCount = 0;
        $centralApiService = app(CentralApiService::class);

        $this->info("بدء مزامنة {$pendingMessages->count()} رسالة...");

        // تجميع الرسائل في مجموعات لتحسين الأداء
        $messageChunks = $pendingMessages->chunk(20);

        foreach ($messageChunks as $chunk) {
            try {
                $messageIds = $chunk->pluck('central_message_id')->toArray();
                $result = $centralApiService->syncMessageStatuses($messageIds);

                if ($result['success']) {
                    foreach ($chunk as $message) {
                        $centralId = $message->central_message_id;

                        if (isset($result['statuses'][$centralId])) {
                            $statusData = $result['statuses'][$centralId];
                            $newStatus = $statusData['status'];

                            if ($newStatus !== $message->status) {
                                $updateData = [];

                                if ($statusData['sent_at']) {
                                    $updateData['sent_at'] = $statusData['sent_at'];
                                }
                                if ($statusData['delivered_at']) {
                                    $updateData['delivered_at'] = $statusData['delivered_at'];
                                }
                                if ($statusData['read_at']) {
                                    $updateData['read_at'] = $statusData['read_at'];
                                }
                                if ($statusData['error_message']) {
                                    $updateData['error_message'] = $statusData['error_message'];
                                }

                                $message->updateStatus($newStatus, $updateData);
                                $syncCount++;

                                $this->line("تم تحديث حالة الرسالة #{$message->id} إلى {$newStatus}");
                            }
                        }
                    }
                } else {
                    $this->warn("فشل في مزامنة مجموعة من الرسائل: {$result['error']}");
                    Log::warning("Failed to sync message chunk: {$result['error']}");
                }

                // تأخير قصير بين المجموعات
                usleep(200000); // 0.2 ثانية

            } catch (\Exception $e) {
                $this->error("خطأ في مزامنة مجموعة من الرسائل: {$e->getMessage()}");
                Log::error("Error syncing message chunk: {$e->getMessage()}");
            }
        }

        $this->info("تم مزامنة {$syncCount} رسالة");
        Log::info("Message sync completed: {$syncCount} messages updated");

        return Command::SUCCESS;
    }
}
