<?php

namespace App\Services;

use App\Jobs\SendMessageJob;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * نقطة موحّدة لإرسال تنبيهات إدارية عبر واتساب (طلبات موافقة، تنبيهات فشل، تذكيرات، تنبيهات صحة
 * النظام...) لرقم أو أكثر من أرقام المسؤولين. printing.alert_phone_number يدعم الآن أكثر من رقم
 * مفصولة بفواصل (مثال: "966501111111,966502222222") — كل رقم يستلم نسخة مستقلة من التنبيه، وأي
 * منهم يمكنه الرد بأوامر الموافقة (وافق/رفض/...) لأن isAdmin() تتحقق من القائمة كاملة.
 */
class AdminNotifier
{
    /**
     * قائمة أرقام المسؤولين المُهيّأة (966...)، بلا تكرار وبلا قيم فارغة.
     */
    public function phones(): array
    {
        $raw = config('printing.alert_phone_number', '');
        if (empty($raw)) {
            return [];
        }

        $phones = array_map([$this, 'formatPhoneNumber'], array_filter(array_map('trim', explode(',', $raw))));

        return array_values(array_unique(array_filter($phones)));
    }

    /**
     * هل هذا الرقم (بأي صيغة وصل بها من الويبهوك) يطابق أحد أرقام المسؤولين المُعدَّة؟
     */
    public function isAdmin(string $phone): bool
    {
        return in_array($this->formatPhoneNumber($phone), $this->phones(), true);
    }

    /**
     * إرسال نص تنبيه لكل أرقام المسؤولين المُعدَّة (نسخة رسالة مستقلة لكل رقم). لا شيء يحدث بصمت
     * إن لم يُعدَّ أي رقم مسؤول — الاستدعاء آمن دوماً.
     */
    public function notify(string $text, array $metadata = []): void
    {
        foreach ($this->phones() as $phone) {
            try {
                $message = Message::create([
                    'phone_number' => $phone,
                    'message_text' => $text,
                    'message_type' => 'text',
                    'status' => 'pending',
                    'metadata' => $metadata ?: null,
                ]);

                dispatch(new SendMessageJob($message->id));
            } catch (Throwable $e) {
                Log::error('AdminNotifier: failed to dispatch admin notification', [
                    'phone' => $phone,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * إرسال ملف (وليس نصاً) لكل أرقام المسؤولين — تُستخدم لمعاينة ملف قبل اتخاذ قرار الموافقة.
     */
    public function notifyFile(string $filePath, ?string $fileName, ?string $fileType, array $metadata = []): void
    {
        foreach ($this->phones() as $phone) {
            try {
                $message = Message::create([
                    'phone_number' => $phone,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'message_type' => 'media',
                    'status' => 'pending',
                    'metadata' => $metadata ?: null,
                ]);

                dispatch(new SendMessageJob($message->id));
            } catch (Throwable $e) {
                Log::error('AdminNotifier: failed to dispatch admin file notification', [
                    'phone' => $phone,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * توحيد صيغة رقم الجوال إلى الصيغة الدولية (966...) — نفس منطق التطبيع المستخدم في باقي
     * النظام (MessageController/MonitorFolderCommand)، مكرَّر هنا عمداً لإبقاء هذه الخدمة مستقلة
     * بلا اعتماد على أي Controller.
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        return format_phone_number($phoneNumber) ?? '';
    }
}
