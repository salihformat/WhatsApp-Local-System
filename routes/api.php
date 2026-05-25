<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ReportController;

// مسار فحص الحالة (عام - لمراقبة الخدمة)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'system' => 'local',
        'timestamp' => now()->toISOString(),
        'server_time' => now()->format('Y-m-d H:i:s'),
        'timezone' => config('app.timezone')
    ]);
});

// Webhook من النظام المركزي (محمي بتحقق التوكن داخل الـ Controller)
Route::post('/webhook/status', [MessageController::class, 'updateStatus']);

// مسارات محمية بتوكن API + Rate Limiting
Route::middleware(['api.token', 'api.ratelimit:30,1'])->group(function () {
    // إرسال رسالة (محدود بـ 30 طلب/دقيقة)
    Route::post('/send-message', [MessageController::class, 'apiSendMessage']);
});

// مسارات محمية بتوكن API فقط
Route::middleware(['api.token'])->group(function () {
    // إدارة الرسائل
    Route::get('/messages', [MessageController::class, 'apiIndex']);
    Route::get('/messages/{id}', [MessageController::class, 'apiShow']);
    Route::post('/messages/{id}/retry', [MessageController::class, 'retry']);

    // التقارير
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/export', [ReportController::class, 'export']);
});

// مسار المستخدم (محمي بـ Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
