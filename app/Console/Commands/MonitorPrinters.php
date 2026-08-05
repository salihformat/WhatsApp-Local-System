<?php

namespace App\Console\Commands;

use App\Jobs\SendMessageJob;
use App\Models\Message;
use App\Models\Printer;
use App\Models\PrinterStatusLog;
use App\Services\PrinterMonitorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonitorPrinters extends Command
{
    protected $signature = 'monitor:printers';

    protected $description = 'فحص حالة الطابعات الفعلية (متصلة/ورق/حبر) وتسجيل النتيجة، وتنبيه عند تغيّر الحالة';

    public function handle(PrinterMonitorService $monitor): int
    {
        $printers = Printer::active()->get();

        if ($printers->isEmpty()) {
            $this->info('لا توجد طابعات مفعّلة لفحصها.');
            return self::SUCCESS;
        }

        foreach ($printers as $printer) {
            $result = $monitor->check($printer);
            $statusChanged = $printer->last_status !== $result['status'];

            PrinterStatusLog::create([
                'printer_id' => $printer->id,
                'status' => $result['status'],
                'is_healthy' => $result['is_healthy'],
                'detail' => $result['detail'],
                'status_changed' => $statusChanged,
            ]);

            $printer->update([
                'last_status' => $result['status'],
                'last_status_healthy' => $result['is_healthy'],
                'last_status_detail' => $result['detail'],
                'last_checked_at' => now(),
            ]);

            $this->line("{$printer->name}: {$result['status']} ({$result['detail']})");

            if ($statusChanged) {
                $this->sendAlertIfConfigured($printer, $result);
            }
        }

        return self::SUCCESS;
    }

    private function sendAlertIfConfigured(Printer $printer, array $result): void
    {
        $alertPhone = config('printing.alert_phone_number');
        if (empty($alertPhone)) {
            return;
        }

        $icon = $result['is_healthy'] ? '✅' : '⚠️';
        $text = $result['is_healthy']
            ? "{$icon} عادت الطابعة \"{$printer->name}\" للعمل بشكل طبيعي."
            : "{$icon} تنبيه: الطابعة \"{$printer->name}\" بها مشكلة — {$result['detail']}";

        try {
            $message = Message::create([
                'phone_number' => $alertPhone,
                'message_text' => $text,
                'message_type' => 'text',
                'status' => 'pending',
            ]);

            dispatch(new SendMessageJob($message->id));

            Log::info('PrinterMonitor: Alert dispatched', [
                'printer' => $printer->name,
                'status' => $result['status'],
            ]);
        } catch (\Exception $e) {
            Log::error('PrinterMonitor: Failed to dispatch alert message', ['error' => $e->getMessage()]);
        }
    }
}
