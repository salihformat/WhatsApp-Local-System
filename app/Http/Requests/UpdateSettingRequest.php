<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // معلومات النظام والجهاز
            'LOCAL_SYSTEM_NAME' => 'nullable|string|max:255',
            'DEVICE_NAME' => 'nullable|string|max:255',
            'LOCATION' => 'nullable|string|max:255',
            'PLAN_TYPE' => 'nullable|string|max:255',

            // الاتصال بالنظام المركزي
            'CENTRAL_API_COMPANY_ID' => 'nullable|integer',
            'CENTRAL_API_TOKEN' => 'nullable|string|max:255',
            'CENTRAL_API_RETRY_ATTEMPTS' => 'nullable|integer|min:1|max:20',
            'CENTRAL_API_RETRY_DELAY' => 'nullable|integer|min:1|max:120',

            // إعدادات الملفات
            'FILE_STORAGE_PATH' => 'nullable|string|max:255',
            'FILE_MAX_SIZE_MB' => 'nullable|integer|min:1|max:100',
            'FILE_ALLOWED_TYPES' => 'nullable|string|max:500',
            'FILE_AUTO_DELETE_DAYS' => 'nullable|integer|min:0|max:365',
            'BACKUP_RETENTION_DAYS' => 'nullable|integer|min:1|max:365',

            // إعادة المحاولة
            'MAX_RETRY_ATTEMPTS' => 'nullable|integer|min:1|max:20',
            'RETRY_DELAY_MINUTES' => 'nullable|integer|min:1|max:120',

            // المراقبة والإرسال
            'MONITORING_MESSAGE_TEXT' => 'nullable|string|max:1000',
            'MONITOR_FOLDER_REQUIRE_APPROVAL' => 'nullable|in:true,false',
            'PRINTER_ALERT_PHONE' => 'nullable|string|max:255',

            // استخراج البيانات من الملفات
            'PHONE_EXTRACTION_LABELS' => 'nullable|string|max:500',
            'PHONE_EXTRACTION_EXCLUDE_CONTEXT' => 'nullable|string|max:500',
            'FILE_NUMBER_LABELS' => 'nullable|string|max:500',
            'PHONE_MATCH_MODE' => 'nullable|in:partial,exact',
            'ENABLE_UNLABELED_PHONE_FALLBACK' => 'nullable|in:true,false',
            'PHONE_REVIEW_REQUIRED_SOURCES' => 'nullable|string|max:500',

            // الطباعة الذكية
            'PRINTING_ENABLED' => 'nullable|in:true,false',
            'PRINTABLE_EXTENSIONS' => 'nullable|string|max:500',
            'PRINT_IMAGE_PAGE_SIZE' => 'nullable|in:a4,letter',
            'PRINT_IMAGE_DPI' => 'nullable|integer|min:72|max:600',
            'PRINTING_APPROVAL_REMINDER_AFTER_MINUTES' => 'nullable|integer|min:0|max:1440',
            'PRINTING_APPROVAL_REMINDER_REPEAT_MINUTES' => 'nullable|integer|min:0|max:1440',
            'PRINTING_REPLY_STATUS_TO_SENDER' => 'nullable|in:true,false',
            'PRINTING_REPLY_ACK_ON_RECEIPT' => 'nullable|in:true,false',
            'PRINTING_NOTIFY_OWNER_ON_JOB_FAILURE' => 'nullable|in:true,false',

            // توزيع المحادثات
            'CONVERSATION_DISTRIBUTION_MODE' => 'nullable|in:manual,specific,all',
            'CONVERSATION_DISTRIBUTION_USER_IDS' => 'nullable|string|max:255',

            // صحة النظام
            'HEALTH_ALERT_QUEUE_BACKLOG_THRESHOLD' => 'nullable|integer|min:1|max:10000',
            'HEALTH_ALERT_COOLDOWN_MINUTES' => 'nullable|integer|min:0|max:1440',
        ];
    }
}
