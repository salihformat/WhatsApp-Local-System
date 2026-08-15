<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * قراءة إعداد من قاعدة البيانات (عبر الكاش).
     * بديل مباشر لـ env('KEY') للإعدادات التشغيلية المنقولة لجدول settings.
     *
     * @param  string     $key      مفتاح الإعداد (مثال: 'PRINTER_ALERT_PHONE')
     * @param  mixed|null $default  القيمة الافتراضية إذا لم يُوجد الإعداد في DB
     * @return mixed
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}
