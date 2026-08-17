<?php
// ملف: app/Console/Commands/MonitorSystem.php (النظام المحلي)

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Message;
use App\Models\Setting;
use App\Models\SystemHealthLog;
use App\Services\AdminNotifier;
use Illuminate\Support\Facades\Cache;
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
        $queueBacklogThreshold = (int) config('app.health_alert_queue_backlog_threshold', 50);
        $queueBacklogCount = DB::table('jobs')->count();
        if ($queueBacklogCount > $queueBacklogThreshold) {
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

        $this->maybeAlertAdmin($queueBacklogCount, $queueBacklogThreshold, $oldPendingMessages);

        $this->newLine();
    }

    /**
     * ينبّه المسؤول عبر واتساب عند مؤشرات تعطل حقيقية (تراكم كبير في الطابور و/أو رسائل معلّقة منذ
     * أكثر من 10 دقائق) — أشيع سبب فعلي رُصد له هو توقف عامل الطابور (Queue Worker) بصمت. يُرسَل
     * تنبيه واحد فقط ثم يلتزم بفترة تهدئة (health_alert_cooldown_minutes) قبل تكراره ما دامت المشكلة
     * مستمرة (بدل تنبيه كل 10 دقائق طوال فترة التعطل)، ويُرسَل إشعار تعافٍ منفصل بمجرد زوال المشكلة.
     */
    private function maybeAlertAdmin(int $queueBacklogCount, int $queueBacklogThreshold, int $oldPendingMessages): void
    {
        $isUnhealthy = $queueBacklogCount > $queueBacklogThreshold || $oldPendingMessages > 0;
        $wasActive = Cache::get('system_health_alert_active', false);

        if ($isUnhealthy) {
            $cooldown = (int) config('app.health_alert_cooldown_minutes', 60);
            if ($cooldown <= 0) {
                return;
            }

            $lastSentAt = Cache::get('system_health_alert_last_sent_at');
            if ($lastSentAt && now()->diffInMinutes($lastSentAt) < $cooldown) {
                return;
            }

            $text = "🚨 تنبيه صحة النظام (من النظام المحلي)\n"
                . $this->systemIdentityLine()
                . ($queueBacklogCount > $queueBacklogThreshold ? "تراكم في طابور المعالجة: {$queueBacklogCount} مهمة بانتظار المعالجة (الحد المسموح: {$queueBacklogThreshold})\n" : '')
                . ($oldPendingMessages > 0 ? "{$oldPendingMessages} رسالة معلّقة منذ أكثر من 10 دقائق بلا إرسال\n" : '')
                . "قد يشير هذا لتوقف عامل الطابور (Queue Worker) بصمت — تحقق من لوحة التحكم وأعد تشغيله إن لزم.";

            app(AdminNotifier::class)->notify($text, ['source' => 'system_health_alert']);

            Cache::put('system_health_alert_active', true, now()->addDay());
            Cache::put('system_health_alert_last_sent_at', now(), now()->addDay());
        } elseif ($wasActive) {
            app(AdminNotifier::class)->notify(
                "✅ عادت صحة النظام لوضعها الطبيعي (من النظام المحلي)\n"
                . $this->systemIdentityLine()
                . 'طابور المعالجة والرسائل المعلّقة.',
                ['source' => 'system_health_recovered']
            );

            Cache::forget('system_health_alert_active');
            Cache::forget('system_health_alert_last_sent_at');
        }
    }

    /**
     * سطر يعرّف بمصدر التنبيه (اسم الجهة/الفرع واسم الجهاز) — ضروري لأن المستخدم يشغّل
     * عدة تنصيبات من النظام المحلي في أماكن مختلفة، والتنبيه بدون هذا السطر لا يوضّح
     * أيها المتعطل فعلياً.
     */
    private function systemIdentityLine(): string
    {
        $systemName = Setting::get('LOCAL_SYSTEM_NAME');
        $deviceName = config('app.device_name');
        $location = config('app.location');

        $parts = [];
        if (!empty($systemName)) $parts[] = "الجهة: {$systemName}";
        if (!empty($location) && $location !== $systemName) $parts[] = "الموقع: {$location}";
        if (!empty($deviceName)) $parts[] = "الجهاز: {$deviceName}";

        return $parts ? implode(' | ', $parts) . "\n" : '';
    }
}
