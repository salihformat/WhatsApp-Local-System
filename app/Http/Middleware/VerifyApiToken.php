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
        $token = $request->header('Authorization');
        $expectedToken = 'Bearer ' . config('app.central_api_token');

        // يجب أن يكون التوكن موجوداً ومطابقاً
        if (empty($token) || $token !== $expectedToken) {
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
