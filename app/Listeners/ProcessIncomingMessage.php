<?php

namespace App\Listeners;

use App\Events\MessageReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * يعالج الأحداث الجانبية بعد استقبال رسالة
 * لا يُكرر منطق إنشاء المحادثة (يتم في Controller)
 * يقوم بمهام إضافية: تسجيل النشاط، الإشعارات المستقبلية
 */
class ProcessIncomingMessage implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     * يتم تنفيذ هذا بعد أن يكون Controller قد أنشأ المحادثة وربط الرسالة
     */
    public function handle(MessageReceived $event): void
    {
        $message = $event->message;

        // تأكد أن الرسالة مرتبطة بمحادثة
        if (!$message->conversation_id) {
            Log::warning('ProcessIncomingMessage: Message has no conversation_id', [
                'message_id' => $message->id,
            ]);
            return;
        }

        $conversation = $message->conversation;

        if (!$conversation) {
            Log::warning('ProcessIncomingMessage: Conversation not found', [
                'message_id' => $message->id,
                'conversation_id' => $message->conversation_id,
            ]);
            return;
        }

        // تسجيل نشاط استقبال الرسالة
        $conversation->activities()->create([
            'type' => 'message_received',
            'description' => 'تم استلام رسالة جديدة',
            'properties' => [
                'message_id' => $message->id,
                'message_type' => $message->message_type,
                'phone_number' => $message->phone_number,
            ],
        ]);

        // TODO: إرسال إشعار للوكيل المُعيّن (مستقبلي)
        // TODO: إشعار Desktop Agent (مستقبلي)
    }
}
