<?php

namespace App\Channels;

use App\Jobs\SendMessageJob;
use App\Models\Message;
use Illuminate\Notifications\Notification;

/**
 * قناة إشعارات مخصصة تُرسل عبر واتساب (بدل بريد/رسائل نصية) — تنشئ سجل Message عادي وتُدرجه في
 * نفس طابور الإرسال المستخدم في كل النظام (SendMessageJob)، تماماً كأي رسالة صادرة أخرى. الإشعار
 * يجب أن يوفر دالة toWhatsApp($notifiable) تُعيد نص الرسالة، والـ notifiable يجب أن يملك
 * routeNotificationForWhatsApp() أو خاصية/دالة phone_number.
 */
class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $phoneNumber = method_exists($notifiable, 'routeNotificationForWhatsApp')
            ? $notifiable->routeNotificationForWhatsApp($notification)
            : ($notifiable->phone_number ?? null);

        if (empty($phoneNumber)) {
            return;
        }

        $text = $notification->toWhatsApp($notifiable);
        if (empty($text)) {
            return;
        }

        $message = Message::create([
            'phone_number' => $phoneNumber,
            'message_text' => $text,
            'message_type' => 'text',
            'status' => 'pending',
            'metadata' => ['source' => 'employee_notification', 'notification_type' => get_class($notification)],
        ]);

        dispatch(new SendMessageJob($message->id));
    }
}
