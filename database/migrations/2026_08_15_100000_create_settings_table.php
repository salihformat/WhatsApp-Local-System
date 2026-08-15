<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            // مجموعة التصنيف — تُستخدم لعرض الإعدادات مرتبة حسب الأقسام في واجهة /settings
            $table->string('group')->default('general');
            $table->timestamps();

            $table->index('group');
        });

        // زرع القيم الأولية من .env مباشرة ضمن الـ Migration لضمان التنفيذ التلقائي
        $this->seedFromEnv();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }

    /**
     * نقل القيم الحالية من .env إلى جدول settings كبذرة أولية (migration seed).
     * إذا كان المتغير غير موجود في .env، تُستخدم القيمة الافتراضية.
     */
    private function seedFromEnv(): void
    {
        $now = now();

        $settings = [
            // مجموعة 1: معلومات النظام والجهاز
            ['key' => 'LOCAL_SYSTEM_NAME', 'value' => env('LOCAL_SYSTEM_NAME', 'فرع الرياض'), 'group' => 'system_info'],
            ['key' => 'DEVICE_NAME', 'value' => env('DEVICE_NAME', 'Unknown Device'), 'group' => 'system_info'],
            ['key' => 'LOCATION', 'value' => env('LOCATION', 'Unknown Location'), 'group' => 'system_info'],
            ['key' => 'PLAN_TYPE', 'value' => env('PLAN_TYPE', 'Standard'), 'group' => 'system_info'],

            // مجموعة 2: الاتصال بالنظام المركزي
            ['key' => 'CENTRAL_API_COMPANY_ID', 'value' => env('CENTRAL_API_COMPANY_ID', ''), 'group' => 'central_api'],
            ['key' => 'CENTRAL_API_TOKEN', 'value' => env('CENTRAL_API_TOKEN', ''), 'group' => 'central_api'],
            ['key' => 'CENTRAL_API_RETRY_ATTEMPTS', 'value' => env('CENTRAL_API_RETRY_ATTEMPTS', '3'), 'group' => 'central_api'],
            ['key' => 'CENTRAL_API_RETRY_DELAY', 'value' => env('CENTRAL_API_RETRY_DELAY', '5'), 'group' => 'central_api'],

            // مجموعة 3: إعدادات الملفات
            ['key' => 'FILE_STORAGE_PATH', 'value' => env('FILE_STORAGE_PATH', 'invoices'), 'group' => 'files'],
            ['key' => 'FILE_MAX_SIZE_MB', 'value' => env('FILE_MAX_SIZE_MB', '20'), 'group' => 'files'],
            ['key' => 'FILE_ALLOWED_TYPES', 'value' => env('FILE_ALLOWED_TYPES', 'pdf,jpg,jpeg,png,gif,bmp,tif,tiff,doc,docx,xls,xlsx,csv,txt'), 'group' => 'files'],
            ['key' => 'FILE_AUTO_DELETE_DAYS', 'value' => env('FILE_AUTO_DELETE_DAYS', '3'), 'group' => 'files'],
            ['key' => 'BACKUP_RETENTION_DAYS', 'value' => env('BACKUP_RETENTION_DAYS', '14'), 'group' => 'files'],

            // مجموعة 4: إعادة المحاولة
            ['key' => 'MAX_RETRY_ATTEMPTS', 'value' => env('MAX_RETRY_ATTEMPTS', '3'), 'group' => 'retry'],
            ['key' => 'RETRY_DELAY_MINUTES', 'value' => env('RETRY_DELAY_MINUTES', '5'), 'group' => 'retry'],

            // مجموعة 5: المراقبة والإرسال
            ['key' => 'MONITORING_MESSAGE_TEXT', 'value' => env('MONITORING_MESSAGE_TEXT', 'مرفق لكم المستند المطلوب'), 'group' => 'monitoring'],
            ['key' => 'MONITOR_FOLDER_REQUIRE_APPROVAL', 'value' => env('MONITOR_FOLDER_REQUIRE_APPROVAL', 'false'), 'group' => 'monitoring'],
            ['key' => 'PRINTER_ALERT_PHONE', 'value' => env('PRINTER_ALERT_PHONE', ''), 'group' => 'monitoring'],

            // مجموعة 6: استخراج البيانات من الملفات
            ['key' => 'PHONE_EXTRACTION_LABELS', 'value' => env('PHONE_EXTRACTION_LABELS', 'رقم الجوال,phone'), 'group' => 'extraction'],
            ['key' => 'PHONE_EXTRACTION_EXCLUDE_CONTEXT', 'value' => env('PHONE_EXTRACTION_EXCLUDE_CONTEXT', 'المحل, الشركة,مكتبنا,store,shop,company,Mobile no.'), 'group' => 'extraction'],
            ['key' => 'FILE_NUMBER_LABELS', 'value' => env('FILE_NUMBER_LABELS', 'رقم الملف, الملف رقم,ملف رقم,file no'), 'group' => 'extraction'],
            ['key' => 'PHONE_MATCH_MODE', 'value' => env('PHONE_MATCH_MODE', 'exact'), 'group' => 'extraction'],
            ['key' => 'ENABLE_UNLABELED_PHONE_FALLBACK', 'value' => env('ENABLE_UNLABELED_PHONE_FALLBACK', 'true'), 'group' => 'extraction'],
            ['key' => 'PHONE_REVIEW_REQUIRED_SOURCES', 'value' => env('PHONE_REVIEW_REQUIRED_SOURCES', ''), 'group' => 'extraction'],

            // مجموعة 7: الطباعة الذكية
            ['key' => 'PRINTING_ENABLED', 'value' => env('PRINTING_ENABLED', 'true'), 'group' => 'printing'],
            ['key' => 'PRINTABLE_EXTENSIONS', 'value' => env('PRINTABLE_EXTENSIONS', 'pdf,jpg,jpeg,png,gif,bmp,tif,tiff,doc,docx,xls,xlsx,ppt,pptx'), 'group' => 'printing'],
            ['key' => 'PRINT_IMAGE_PAGE_SIZE', 'value' => env('PRINT_IMAGE_PAGE_SIZE', 'a4'), 'group' => 'printing'],
            ['key' => 'PRINT_IMAGE_DPI', 'value' => env('PRINT_IMAGE_DPI', '200'), 'group' => 'printing'],
            ['key' => 'PRINTING_APPROVAL_REMINDER_AFTER_MINUTES', 'value' => env('PRINTING_APPROVAL_REMINDER_AFTER_MINUTES', '20'), 'group' => 'printing'],
            ['key' => 'PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES', 'value' => env('PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES', '30'), 'group' => 'printing'],
            ['key' => 'PRINTING_REPLY_STATUS_TO_SENDER', 'value' => env('PRINTING_REPLY_STATUS_TO_SENDER', 'true'), 'group' => 'printing'],
            ['key' => 'PRINTING_REPLY_ACK_ON_RECEIPT', 'value' => env('PRINTING_REPLY_ACK_ON_RECEIPT', 'true'), 'group' => 'printing'],
            ['key' => 'PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE', 'value' => env('PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE', 'true'), 'group' => 'printing'],

            // مجموعة 8: توزيع المحادثات
            ['key' => 'CONVERSATION_DISTRIBUTION_MODE', 'value' => env('CONVERSATION_DISTRIBUTION_MODE', 'manual'), 'group' => 'distribution'],
            ['key' => 'CONVERSATION_DISTRIBUTION_USER_IDS', 'value' => env('CONVERSATION_DISTRIBUTION_USER_IDS', ''), 'group' => 'distribution'],

            // مجموعة 9: صحة النظام
            ['key' => 'HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD', 'value' => env('HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD', '50'), 'group' => 'health'],
            ['key' => 'HEALTH_ALERT_COOLDOWN_MINUTES', 'value' => env('HEALTH_ALERT_COOLDOWN_MINUTES', '60'), 'group' => 'health'],
        ];

        foreach ($settings as &$setting) {
            // تحويل القيم غير النصية (bool, int) إلى نص للتخزين
            $setting['value'] = (string) ($setting['value'] ?? '');
            $setting['created_at'] = $now;
            $setting['updated_at'] = $now;
        }

        \Illuminate\Support\Facades\DB::table('settings')->insert($settings);
    }
};
