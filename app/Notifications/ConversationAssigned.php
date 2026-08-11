<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * تُرسَل للموظف عند تعيين محادثة له (تلقائياً عبر ConversationDistributionService، أو يدوياً من
 * صفحة المحادثة) — عبر قناتين: إشعار داخل النظام (جرس الإشعارات بالأعلى، دائماً)، ورسالة واتساب
 * فعلية لرقمه الشخصي إن كان مسجَّلاً (User::phone_number، راجع WhatsAppChannel) — كلاهما يتضمن آخر
 * رسالة من العميل نفسها حتى يعرف سياق الطلب فوراً دون فتح المحادثة أولاً.
 */
class ConversationAssigned extends Notification
{
    use Queueable;

    public function __construct(
        private Conversation $conversation,
        private ?Message $lastMessage = null,
        private string $assignedByLabel = 'تلقائياً حسب إعداد توزيع المحادثات'
    ) {
    }

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (!empty($notifiable->phone_number)) {
            $channels[] = WhatsAppChannel::class;
        }

        return $channels;
    }

    public function toWhatsApp(object $notifiable): string
    {
        $customerName = $this->conversation->contact?->name;
        $customerLabel = $customerName ? "{$customerName} ({$this->conversation->phone_number})" : $this->conversation->phone_number;

        return "📨 تم تعيين محادثة جديدة لك\n"
            . "العميل: {$customerLabel}\n"
            . "آخر رسالة: {$this->messagePreview()}\n"
            . "بواسطة: {$this->assignedByLabel}\n\n"
            . "افتح المحادثة: " . route('conversations.show', $this->conversation);
    }

    public function toArray(object $notifiable): array
    {
        $customerName = $this->conversation->contact?->name ?: $this->conversation->phone_number;

        return [
            'conversation_id' => $this->conversation->id,
            'customer_name' => $customerName,
            'phone_number' => $this->conversation->phone_number,
            'message_preview' => $this->messagePreview(),
            'assigned_by' => $this->assignedByLabel,
            'url' => route('conversations.show', $this->conversation),
        ];
    }

    /**
     * نص مختصر لآخر رسالة من العميل — يُقتطع إلى 120 حرفاً، ويُستبدل بوصف نوع الملف إن كانت
     * رسالة وسائط بلا نص (صورة/مستند بلا تعليق نصي، وهي الحالة الشائعة لمرفقات واتساب).
     */
    private function messagePreview(): string
    {
        if (!$this->lastMessage) {
            return '(بلا رسائل بعد)';
        }

        if (!empty($this->lastMessage->message_text)) {
            $text = trim($this->lastMessage->message_text);
            return mb_strlen($text) > 120 ? mb_substr($text, 0, 120) . '…' : $text;
        }

        return match ($this->lastMessage->message_type) {
            'media' => '📎 مرفق (' . ($this->lastMessage->file_name ?: 'ملف') . ')',
            default => '(بلا نص)',
        };
    }
}
