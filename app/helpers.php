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

if (!function_exists('format_phone_number')) {
    /**
     * تنسيق رقم الجوال ذكياً بناءً على كود الدولة الافتراضي
     * يقوم بمسح الرموز وإضافة كود الدولة إذا كان مفقوداً أو يحل محل الصفر
     */
    function format_phone_number(?string $phoneNumber): ?string
    {
        if (empty($phoneNumber)) return null;

        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        $countryCode = setting('DEFAULT_COUNTRY_CODE', '966');

        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = $countryCode . substr($phoneNumber, 1);
        } elseif (strlen($phoneNumber) === 9 && $countryCode === '966') {
            // legacy fallback for Saudi numbers passed without zero
            $phoneNumber = '966' . $phoneNumber;
        } elseif (strlen($phoneNumber) >= 8 && substr($phoneNumber, 0, strlen($countryCode)) !== $countryCode) {
            // If it doesn't start with the country code, and it's long enough to be a local number, prepend it
            // Only prepend if the number seems like a local number (e.g. 10 digits without leading 0 is rare, but let's be safe)
            // It's safer to just prepend if it doesn't match the prefix and doesn't have a 0.
            $phoneNumber = $countryCode . $phoneNumber;
        }

        return $phoneNumber;
    }
}
