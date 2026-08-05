<?php
// ملف: app/Console/Commands/MonitorSystem.php (النظام المحلي)

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Models\SystemHealthLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MonitorSystem extends Command
{
    protected $signature = 'monitor:system {--interval=30}';
    protected $description = 'Monitor system health continuously';

    public function handle()
    {
        $interval = (int) $this->option('interval');

        // عند interval=0 (كما يستدعيه Scheduler كل 10 دقائق)، نفّذ فحصاً واحداً فقط وننهي الأمر،
        // بدل الدخول في حلقة لا نهائية بلا تأخير كانت تجمّد باقي التشغيلات المجدولة إلى الأبد
        // (withoutOverlapping تعتبر الأمر "لا يزال يعمل" ما دام لم يخرج من handle()).
        if ($interval <= 0) {
            $this->performHealthCheck();
            return Command::SUCCESS;
        }

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
        $centralConnected = false;
        $responseTimeMs = null;
        $centralError = null;

        try {
            $centralUrl = config('app.central_api_url');
            $healthUrl = str_replace('/api', '/api/health', $centralUrl);
            $start = microtime(true);
            $response = Http::timeout(5)->get($healthUrl);
            $responseTimeMs = (int) round((microtime(true) - $start) * 1000);

            if ($response->successful()) {
                $centralConnected = true;
                $this->line("   ✅ Central system: OK ({$responseTimeMs}ms)");
            } else {
                $centralError = "HTTP {$response->status()}";
                $this->line("   ❌ Central system: {$centralError}");
            }
        } catch (\Exception $e) {
            $centralError = 'Connection failed';
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

        // عدد المهام المتراكمة في طابور Laravel (queue jobs لم تُعالَج بعد) — مؤشر حاسم
        // اكتُشفت أهميته فعلياً بعد رصد تراكم 825 مهمة بسبب توقف عامل الطابور لفترة طويلة
        $queueBacklogCount = DB::table('jobs')->count();
        if ($queueBacklogCount > 50) {
            $this->warn("   ⚠️ تراكم كبير في الطابور: {$queueBacklogCount} مهمة بانتظار المعالجة");
        }

        // الاحتفاظ بـ30 يوماً فقط (144 فحص/يوم تقريباً) لمنع تضخم الجدول إلى ما لا نهاية
        SystemHealthLog::where('checked_at', '<', now()->subDays(30))->delete();

        SystemHealthLog::create([
            'pending_messages' => $pendingMessages,
            'processing_messages' => $processingMessages,
            'failed_messages' => $failedMessages,
            'sent_messages' => $sentMessages,
            'old_pending_count' => $oldPendingMessages,
            'recent_failed_count' => $recentFailedMessages,
            'queue_backlog_count' => $queueBacklogCount,
            'central_connected' => $centralConnected,
            'central_response_time_ms' => $responseTimeMs,
            'central_error' => $centralError,
            'checked_at' => now(),
        ]);

        $this->newLine();
    }
}
