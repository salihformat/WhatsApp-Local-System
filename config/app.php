<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),



    'central_api_url' => env('CENTRAL_API_URL', 'http://localhost:8000/api'),
    'central_api_token' => env('CENTRAL_API_TOKEN'),
    'company_id' => env('CENTRAL_API_COMPANY_ID' ),

    'files_storage_path' => env('FILE_STORAGE_PATH', 'invoices'),
    'files_max_size' => env('FILE_MAX_SIZE_MB', 20) * 1024, // Convert to KB
    'files_allowed_types' => explode(',', env('FILE_ALLOWED_TYPES', 'pdf,jpg,jpeg,png,doc,docx')),
    'file_auto_delete_days' => env('FILE_AUTO_DELETE_DAYS', 3),

    'device_name' => env('DEVICE_NAME', 'Unknown Device'),
    'location' => env('LOCATION', 'Unknown Location'),
    'plan_type' => env('PLAN_TYPE', 'Standard'),

    'max_retry_attempts' => env('MAX_RETRY_ATTEMPTS', 5),
    'retry_delay_minutes' => env('RETRY_DELAY_MINUTES', 5),

    'monitor_folder_path' => env('MONITOR_FOLDER_PATH', 'C:/PrintMonitor'),
    'monitor_interval_seconds' => env('MONITOR_INTERVAL_SECONDS', 10),

    // إذا فُعِّل، يُحجز كل ملف يصل عبر مجلد المراقبة بحالة review_pending وينتظر موافقة صريحة قبل
    // إرساله عبر واتساب (بدل الإرسال التلقائي الفوري)، بصرف النظر عن مستوى الثقة في استخراج رقم
    // الجوال. الموافقة عبر زر في صفحة "متابعة الإرسال" أو رد واتساب "وافق ارسال <رقم>" من رقم المسؤول
    // (printing.alert_phone_number). عند التعطيل (الافتراضي)، يبقى السلوك القديم: إرسال تلقائي فوري
    // إلا لحالات الثقة المنخفضة (راجع PHONE_REVIEW_REQUIRED_SOURCES في MonitorFolderCommand).
    'monitor_folder_require_approval' => (bool) env('MONITOR_FOLDER_REQUIRE_APPROVAL', false),

    // فوق كم مهمة متراكمة في طابور Laravel (jobs غير مُعالَجة) يُعتبر ذلك تعطلاً محتملاً لعامل
    // الطابور (Queue Worker) ويستحق تنبيه واتساب فورياً لرقم PRINTER_ALERT_PHONE؟ يفحصه أمر
    // monitor:system المجدول كل 10 دقائق (راجع routes/console.php).
    'health_alert_queue_backlog_threshold' => (int) env('HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD', 50),

    // بعد إرسال تنبيه صحة النظام، كم دقيقة ننتظر قبل السماح بإرسال تنبيه آخر لنفس المشكلة المستمرة؟
    // يمنع إغراق المسؤول برسالة كل 10 دقائق طوال فترة التعطل. اضبطه على 0 لتعطيل هذه التنبيهات كلياً.
    'health_alert_cooldown_minutes' => (int) env('HEALTH_ALERT_COOLDOWN_MINUTES', 60),

    // عدد الأيام للاحتفاظ بالنسخ الاحتياطية التلقائية لقاعدة البيانات (storage/app/backups) قبل
    // حذفها تلقائياً — راجع أمر backup:database المجدول يومياً في routes/console.php.
    'backup_retention_days' => (int) env('BACKUP_RETENTION_DAYS', 14),

    // وضع توزيع المحادثات الجديدة على المستخدمين (راجع App\Services\ConversationDistributionService):
    // 'manual' (الافتراضي): لا تعيين تلقائي إطلاقاً — تبقى المحادثة الجديدة بلا موظف مسؤول حتى يعيّنها
    //   أحد يدوياً من صفحة المحادثات، أو تطابق قاعدة أتمتة محددة (AutomationRule) تُعيّنها بنفسها.
    // 'specific': توزيع تلقائي عادل (الأقل محادثات مفتوحة حالياً) بين مجموعة مستخدمين محددة
    //   (conversation_distribution_user_ids أدناه) فقط.
    // 'all': نفس التوزيع العادل، لكن بين كل المستخدمين بدور "agent" المتاحين للتعيين
    //   (User::is_available_for_assignment = true)، بلا حاجة لتحديد قائمة يدوياً.
    'conversation_distribution_mode' => env('CONVERSATION_DISTRIBUTION_MODE', 'manual'),

    // قائمة معرّفات المستخدمين (مفصولة بفواصل) المشمولين بالتوزيع التلقائي عند
    // conversation_distribution_mode = specific فقط. مثال: "2,5,7"
    'conversation_distribution_user_ids' => env('CONVERSATION_DISTRIBUTION_USER_IDS', ''),


    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'Asia/Riyadh',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

];
