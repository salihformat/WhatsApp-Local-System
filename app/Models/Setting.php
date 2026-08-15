<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * نموذج الإعدادات — يوفر واجهة Key-Value لتخزين إعدادات النظام القابلة للتعديل من لوحة التحكم
 * بدلاً من ملف .env. يستخدم Cache لتقليل استعلامات قاعدة البيانات في كل طلب HTTP.
 *
 * @property int    $id
 * @property string $key
 * @property string|null $value
 * @property string $group
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    /**
     * مفتاح الكاش الموحّد لكل الإعدادات.
     */
    private const CACHE_KEY = 'app_settings';

    /**
     * قراءة إعداد واحد بالمفتاح.
     * يقرأ من الكاش أولاً (الذي يحتوي كل الإعدادات مُحمَّلة دفعة واحدة)، ثم يعود للقيمة
     * الافتراضية إذا لم يُوجد.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = static::getAllCached();

        return $settings[$key] ?? $default;
    }

    /**
     * كتابة أو تحديث إعداد واحد.
     * لا يمسح الكاش هنا — يُمسح بشكل مجمّع عند الانتهاء من كل التحديثات عبر flushCache().
     */
    public static function set(string $key, ?string $value, ?string $group = null): void
    {
        $data = ['value' => $value ?? ''];
        if ($group !== null) {
            $data['group'] = $group;
        }

        static::updateOrCreate(['key' => $key], $data);
    }

    /**
     * تحديث مجمّع لعدة إعدادات دفعة واحدة ثم مسح الكاش مرة واحدة فقط.
     *
     * @param array<string, string|null> $settings  مصفوفة [key => value]
     */
    public static function setMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::updateOrCreate(
                ['key' => $key],
                ['value' => $value ?? '']
            );
        }

        static::flushCache();
    }

    /**
     * تحميل كل الإعدادات من الكاش (أو من DB إذا لم يكن الكاش موجوداً).
     * يُستخدم في AppServiceProvider::boot() لحقن كل القيم في config() دفعة واحدة.
     *
     * @return array<string, string>  مصفوفة [key => value]
     */
    public static function getAllCached(): array
    {
        return Cache::rememberForever(static::CACHE_KEY, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }

    /**
     * مسح كاش الإعدادات — يُستدعى بعد أي تحديث من صفحة /settings.
     */
    public static function flushCache(): void
    {
        Cache::forget(static::CACHE_KEY);
    }
}
