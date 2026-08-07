<?php

namespace App\Console\Commands;

use App\Jobs\SendMessageJob;
use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * إرسال تنبيه واتساب فوري لصاحب المنشأة (نفس رقم PRINTER_ALERT_PHONE المستخدم لتنبيهات صحة
 * الطابعات) — نقطة دخول عامة يستدعيها أي سكربت خارجي (مثل auto-update.ps1) بلا الحاجة لتكرار
 * منطق الإرسال في كل مكان.
 */
class NotifyOwnerCommand extends Command
{
    protected $signature = 'system:notify-owner {message}';

    protected $description = 'إرسال تنبيه واتساب لصاحب المنشأة عبر رقم PRINTER_ALERT_PHONE';

    public function handle(): int
    {
        $alertPhone = config('printing.alert_phone_number');
        if (empty($alertPhone)) {
            $this->warn('PRINTER_ALERT_PHONE غير مُعَدّ — تم تجاهل التنبيه.');
            return self::SUCCESS;
        }

        try {
            $message = Message::create([
                'phone_number' => $alertPhone,
                'message_text' => $this->argument('message'),
                'message_type' => 'text',
                'status' => 'pending',
            ]);

            dispatch(new SendMessageJob($message->id));

            $this->info('تم إرسال التنبيه.');
        } catch (\Exception $e) {
            Log::error('NotifyOwnerCommand: Failed to dispatch alert message', ['error' => $e->getMessage()]);
            $this->error('فشل إرسال التنبيه: ' . $e->getMessage());
        }

        return self::SUCCESS;
    }
}
