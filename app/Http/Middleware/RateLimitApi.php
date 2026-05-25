<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware لتحديد معدل الطلبات (Rate Limiting) على مسارات API
 * يحمي من إساءة الاستخدام والإرسال المفرط
 */
class RateLimitApi
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = 'rate_limit:' . $request->ip() . ':' . $request->path();
        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'attempts' => $attempts,
                'max_attempts' => $maxAttempts,
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Too many requests. Please try again later.',
                'retry_after' => $decayMinutes * 60,
            ], 429, [
                'Retry-After' => $decayMinutes * 60,
                'X-RateLimit-Limit' => $maxAttempts,
                'X-RateLimit-Remaining' => 0,
            ]);
        }

        Cache::put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        $response = $next($request);

        // إضافة headers لمعلومات Rate Limit
        $response->headers->set('X-RateLimit-Limit', $maxAttempts);
        $response->headers->set('X-RateLimit-Remaining', max(0, $maxAttempts - $attempts - 1));

        return $response;
    }
}
