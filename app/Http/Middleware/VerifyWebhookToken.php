<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class VerifyWebhookToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // [أفضل ممارسة: فصل بيانات الاعتماد حسب حدود الثقة] هذا التوكن (وارد) يتحقق أن الطلب فعلاً
        // من النظام المركزي — منفصل عمداً عن CENTRAL_API_TOKEN (صادر، يستخدمه CentralApiService عند
        // استدعاء API النظام المركزي). قبل هذا الفصل كان المتغيّر نفسه يُستخدم للاتجاهين معاً، فأي
        // تسريب لأحدهما يكشف الطرفين، ولا يمكن تدوير أحدهما بمعزل عن الآخر. راجع /docs.
        //
        // توافق خلفي: إن لم يُضبط CENTRAL_WEBHOOK_TOKEN بعد، نستمر بقبول CENTRAL_API_TOKEN القديم
        // (بلا كسر أي تركيب حالي)، مع تنبيه في السجلات يوصي بضبط توكن مستقل.
        $webhookToken = config('app.central_webhook_token');
        $apiToken = config('app.central_api_token');

        if (empty($webhookToken)) {
            $configuredToken = $apiToken;

            if (!empty($configuredToken)) {
                Log::notice('CENTRAL_WEBHOOK_TOKEN غير مضبوط — يُستخدم CENTRAL_API_TOKEN مؤقتاً للتحقق من الويب هوك الوارد (نفس توكن الاتصال الصادر). يُفضَّل ضبط توكن مستقل من صفحة الإعدادات لفصل بيانات الاعتماد حسب الاتجاه — راجع /docs.');
            }
        } else {
            $configuredToken = $webhookToken;
        }

        // فشل مغلق: إن لم يُضبط أي توكن حقيقي، نرفض كل الطلبات بدل قبول قيمة افتراضية معروفة
        if (empty($configuredToken)) {
            Log::critical('لا يوجد CENTRAL_WEBHOOK_TOKEN ولا CENTRAL_API_TOKEN مضبوطاً. رفض كل طلبات الويب هوك.');
            return response()->json(['error' => 'Server misconfigured'], 500);
        }

        $token = $request->header('Authorization') ?? $request->input('token');
        $expectedToken = 'Bearer ' . $configuredToken;

        // Allow passing raw token or Bearer token
        if (!hash_equals($expectedToken, (string) $token) && !hash_equals($configuredToken, (string) $token)) {
            Log::warning('Unauthorized webhook attempt', [
                'ip' => $request->ip(),
                'path' => $request->path(),
            ]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // طبقة تحقق إضافية: التوقيع HMAC-SHA256 (X-Webhook-Signature) يثبت أن جسم الطلب لم
        // يُعدَّل أثناء النقل، بخلاف التوكن وحده الذي يثبت فقط هوية المُرسِل. النظام المركزي
        // يوقّع بنفس التوكن المُستخدَم أعلاه (endpoint->token يطابق CENTRAL_WEBHOOK_TOKEN إن
        // ضُبط، أو company->security_token إن كان لا يزال يستخدم CENTRAL_API_TOKEN القديم).
        // نتحقق من التوقيع فقط إن أُرسل (توافق خلفي مع أي مُرسِل قديم/خارجي لا يوقّع طلباته).
        $signatureHeader = $request->header('X-Webhook-Signature');
        if (!empty($signatureHeader)) {
            $expectedSignature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $configuredToken);

            if (!hash_equals($expectedSignature, (string) $signatureHeader)) {
                Log::warning('Webhook signature mismatch', [
                    'ip' => $request->ip(),
                    'path' => $request->path(),
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        }

        return $next($request);
    }
}
