<?php

namespace App\Providers;

use App\Listeners\LogAuthenticationEvents;
use App\Models\Setting;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        Event::listen(Login::class, [LogAuthenticationEvents::class, 'handleLogin']);
        Event::listen(Logout::class, [LogAuthenticationEvents::class, 'handleLogout']);
        Event::listen(Failed::class, [LogAuthenticationEvents::class, 'handleFailedLogin']);

        // حقن إعدادات قاعدة البيانات في config() لتُكتب فوق قيم .env —
        // يضمن أن كل استخدامات config('app.xxx') و config('printing.xxx') الحالية
        // تقرأ القيم المُحدَّثة من لوحة التحكم بدلاً من .env الثابت.
        $this->overrideConfigFromDatabase();
    }

    /**
     * تحميل كل الإعدادات من جدول settings (عبر الكاش) وحقنها في مفاتيح config() المناسبة.
     * لا تُنفَّذ إذا لم يكن جدول settings موجوداً بعد (أثناء الـ Migration الأولى).
     */
    private function overrideConfigFromDatabase(): void
    {
        try {
            if (!Schema::hasTable('settings')) {
                return;
            }

            $s = Setting::getAllCached();

            // config/app.php — الإعدادات العامة
            if (isset($s['DEVICE_NAME'])) config(['app.device_name' => $s['DEVICE_NAME']]);
            if (isset($s['LOCATION'])) config(['app.location' => $s['LOCATION']]);
            if (isset($s['PLAN_TYPE'])) config(['app.plan_type' => $s['PLAN_TYPE']]);
            if (isset($s['FILE_STORAGE_PATH'])) config(['app.files_storage_path' => $s['FILE_STORAGE_PATH']]);
            if (isset($s['FILE_MAX_SIZE_MB'])) config(['app.files_max_size' => (int) $s['FILE_MAX_SIZE_MB'] * 1024]);
            if (isset($s['FILE_ALLOWED_TYPES'])) config(['app.files_allowed_types' => explode(',', $s['FILE_ALLOWED_TYPES'])]);
            if (isset($s['FILE_AUTO_DELETE_DAYS'])) config(['app.file_auto_delete_days' => (int) $s['FILE_AUTO_DELETE_DAYS']]);
            if (isset($s['MAX_RETRY_ATTEMPTS'])) config(['app.max_retry_attempts' => (int) $s['MAX_RETRY_ATTEMPTS']]);
            if (isset($s['RETRY_DELAY_MINUTES'])) config(['app.retry_delay_minutes' => (int) $s['RETRY_DELAY_MINUTES']]);
            if (isset($s['CENTRAL_API_COMPANY_ID'])) config(['app.company_id' => $s['CENTRAL_API_COMPANY_ID']]);
            if (isset($s['CENTRAL_API_TOKEN'])) config(['app.central_api_token' => $s['CENTRAL_API_TOKEN']]);
            if (isset($s['MONITOR_FOLDER_REQUIRE_APPROVAL'])) config(['app.monitor_folder_require_approval' => filter_var($s['MONITOR_FOLDER_REQUIRE_APPROVAL'], FILTER_VALIDATE_BOOLEAN)]);
            if (isset($s['HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD'])) config(['app.health_alert_queue_backlog_threshold' => (int) $s['HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD']]);
            if (isset($s['HEALTH_ALERT_COOLDOWN_MINUTES'])) config(['app.health_alert_cooldown_minutes' => (int) $s['HEALTH_ALERT_COOLDOWN_MINUTES']]);
            if (isset($s['BACKUP_RETENTION_DAYS'])) config(['app.backup_retention_days' => (int) $s['BACKUP_RETENTION_DAYS']]);
            if (isset($s['CONVERSATION_DISTRIBUTION_MODE'])) config(['app.conversation_distribution_mode' => $s['CONVERSATION_DISTRIBUTION_MODE']]);
            if (isset($s['CONVERSATION_DISTRIBUTION_USER_IDS'])) config(['app.conversation_distribution_user_ids' => $s['CONVERSATION_DISTRIBUTION_USER_IDS']]);

            // config/printing.php — إعدادات الطباعة الذكية
            if (isset($s['PRINTING_ENABLED'])) config(['printing.enabled' => filter_var($s['PRINTING_ENABLED'], FILTER_VALIDATE_BOOLEAN)]);
            if (isset($s['PRINTABLE_EXTENSIONS'])) config(['printing.printable_extensions' => array_map('trim', explode(',', $s['PRINTABLE_EXTENSIONS']))]);
            if (isset($s['PRINTER_ALERT_PHONE'])) config(['printing.alert_phone_number' => $s['PRINTER_ALERT_PHONE']]);
            if (isset($s['PRINTING_REPLY_STATUS_TO_SENDER'])) config(['printing.reply_status_to_sender' => filter_var($s['PRINTING_REPLY_STATUS_TO_SENDER'], FILTER_VALIDATE_BOOLEAN)]);
            if (isset($s['PRINTING_REPLY_ACK_ON_RECEIPT'])) config(['printing.reply_ack_on_receipt' => filter_var($s['PRINTING_REPLY_ACK_ON_RECEIPT'], FILTER_VALIDATE_BOOLEAN)]);
            if (isset($s['PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE'])) config(['printing.notify_owner_on_job_failure' => filter_var($s['PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE'], FILTER_VALIDATE_BOOLEAN)]);
            if (isset($s['PRINT_IMAGE_PAGE_SIZE'])) config(['printing.image_page_size' => $s['PRINT_IMAGE_PAGE_SIZE']]);
            if (isset($s['PRINT_IMAGE_DPI'])) config(['printing.image_dpi' => (int) $s['PRINT_IMAGE_DPI']]);
            if (isset($s['PRINTING_APPROVAL_REMINDER_AFTER_MINUTES'])) config(['printing.approval_reminder_after_minutes' => (int) $s['PRINTING_APPROVAL_REMINDER_AFTER_MINUTES']]);
            if (isset($s['PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES'])) config(['printing.approval_reminder_repeat_minutes' => (int) $s['PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES']]);

        } catch (\Throwable $e) {
            // أثناء الـ migration أو إذا كانت قاعدة البيانات غير متاحة، نتجاهل الخطأ بصمت
            // ويبقى config() يقرأ من .env كالمعتاد.
            Log::debug('Settings override skipped: ' . $e->getMessage());
        }
    }
}

