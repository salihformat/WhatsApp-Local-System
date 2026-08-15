<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

/**
 * قبل هذه الترحيلة، النظام كان يستخدم CENTRAL_API_TOKEN لغرضين مختلفين تماماً في آن واحد:
 * 1) توكن صادر (Outbound): يُرسله هذا النظام كـ Bearer عند استدعاء API النظام المركزي.
 * 2) توكن وارد (Inbound): يتحقق به VerifyWebhookToken من أن طلبات الويب هوك الواردة فعلاً
 *    من النظام المركزي.
 *
 * توحيد الاتجاهين في سرّ واحد يخالف أفضل ممارسات الأمان (فصل بيانات الاعتماد حسب حدود الثقة):
 * لو تسرّب أحدهما (سجلات، دعم فني، إلخ) يُصبح الطرفان مكشوفين معاً، ولا يمكن تدوير أحدهما بمعزل
 * عن الآخر. النظام المركزي أصلاً يدعم توكناً مستقلاً لكل نقطة ويب هوك مرتبطة (company_webhooks.token
 * بمعزل عن company.security_token) — كان القيد الوحيد من جهة النظام المحلي فقط.
 *
 * CENTRAL_WEBHOOK_TOKEN هذا اختياري تماماً: إن بقي فارغاً، VerifyWebhookToken يستمر بالرجوع إلى
 * CENTRAL_API_TOKEN كما كان (بلا أي كسر للأنظمة المُهيَّأة حالياً)، مع تسجيل تنبيه يوصي بضبط
 * توكن مستقل. راجع القسم الجديد في /docs لخطوات الفصل الكاملة.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Setting::where('key', 'CENTRAL_WEBHOOK_TOKEN')->exists()) {
            Setting::create([
                'key' => 'CENTRAL_WEBHOOK_TOKEN',
                'value' => '',
                'group' => 'central_api',
            ]);
        }

        Setting::flushCache();
    }

    public function down(): void
    {
        Setting::where('key', 'CENTRAL_WEBHOOK_TOKEN')->delete();
        Setting::flushCache();
    }
};
