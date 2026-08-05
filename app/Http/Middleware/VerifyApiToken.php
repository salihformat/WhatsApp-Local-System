<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware للتحقق من صحة API Token للطلبات الواردة
 * يُستخدم لحماية مسارات API من الوصول غير المصرح به
 */
class VerifyApiToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $configuredToken = config('app.central_api_token');

        // فشل مغلق: إن لم يُضبط توكن حقيقي في .env، نرفض كل الطلبات بدل قبول قيمة افتراضية معروفة
        if (empty($configuredToken)) {
            Log::critical('CENTRAL_API_TOKEN is not configured. Rejecting all API requests.');
            return response()->json([
                'success' => false,
                'error' => 'Server misconfigured',
            ], 500);
        }

        $token = $request->header('Authorization');
        $expectedToken = 'Bearer ' . $configuredToken;

        // يجب أن يكون التوكن موجوداً ومطابقاً
        if (empty($token) || !hash_equals($expectedToken, (string) $token)) {
            Log::warning('Unauthorized API access attempt', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Unauthorized - Invalid or missing API token',
            ], 401);
        }

        return $next($request);
    }
}
