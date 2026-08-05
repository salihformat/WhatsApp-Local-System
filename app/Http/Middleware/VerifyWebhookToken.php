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
        $configuredToken = config('app.central_api_token');

        // فشل مغلق: إن لم يُضبط توكن حقيقي في .env، نرفض كل الطلبات بدل قبول قيمة افتراضية معروفة
        if (empty($configuredToken)) {
            Log::critical('CENTRAL_API_TOKEN is not configured. Rejecting all webhook requests.');
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

        return $next($request);
    }
}
