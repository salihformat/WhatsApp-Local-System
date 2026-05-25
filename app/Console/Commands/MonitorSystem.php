<?php
// ملف: app/Console/Commands/MonitorSystem.php (النظام المحلي)

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use Illuminate\Support\Facades\Http;

class MonitorSystem extends Command
{
    protected $signature = 'monitor:system {--interval=30}';
    protected $description = 'Monitor system health continuously';

    public function handle()
    {
        $interval = $this->option('interval');
        $this->info("🔄 Starting continuous monitoring (interval: {$interval}s)");
        $this->info('Press Ctrl+C to stop');
        $this->newLine();

        while (true) {
            $this->performHealthCheck();
            sleep($interval);
        }
    }

    private function performHealthCheck()
    {
        $timestamp = now()->format('Y-m-d H:i:s');
        $this->line("[$timestamp] Performing health check...");

        // فحص الرسائل المعلقة
        $pendingMessages = Message::where('status', 'pending')->count();
        $failedMessages = Message::where('status', 'failed')->count();
        $processingMessages = Message::where('status', 'processing')->count();
        $sentMessages = Message::where('status', 'sent')->count();

        $this->line("   📊 Messages - Pending: {$pendingMessages}, Processing: {$processingMessages}, Failed: {$failedMessages}, Sent: {$sentMessages}");

        // فحص الاتصال بالنظام المركزي
        try {
            $centralUrl = config('app.central_api_url');
            $healthUrl = str_replace('/api', '/api/health', $centralUrl);
            $response = Http::timeout(5)->get($healthUrl);

            if ($response->successful()) {
                $this->line('   ✅ Central system: OK');
            } else {
                $this->line("   ❌ Central system: HTTP {$response->status()}");
            }
        } catch (\Exception $e) {
            $this->line('   ❌ Central system: Connection failed');
        }

        // تحذير إذا كان هناك رسائل معلقة لفترة طويلة
        $oldPendingMessages = Message::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(10))
            ->count();

        if ($oldPendingMessages > 0) {
            $this->warn("   ⚠️ {$oldPendingMessages} messages pending for >10 minutes");
        }

        // تحذير إذا كان هناك رسائل فاشلة كثيرة
        $recentFailedMessages = Message::where('status', 'failed')
            ->where('updated_at', '>', now()->subHour())
            ->count();

        if ($recentFailedMessages > 5) {
            $this->warn("   ⚠️ {$recentFailedMessages} messages failed in the last hour");
        }

        $this->newLine();
    }
}
