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

// Webhook من النظام المركزي (محمي عبر VerifyWebhookToken Middleware)
Route::middleware([\App\Http\Middleware\VerifyWebhookToken::class])->group(function () {
    Route::post('/webhook/status', [MessageController::class, 'updateStatus']);
    Route::post('/webhook/status_update', [MessageController::class, 'updateStatus']); // لدعم المسار التلقائي
    Route::post('/webhook/ping', [MessageController::class, 'updateStatus']); // مسار اختبار الاتصال القديم
    Route::post('/webhook/incoming_message', [MessageController::class, 'incomingMessage']);
});

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

    // مسارات وهمية (Mock) لجهات الاتصال لمحاكاة النظام المركزي وتجنب الخطأ 404
    Route::get('/contacts', function (Request $request) {
        return response()->json([
            'success' => true,
            'contacts' => [], // يمكن إضافة جهات اتصال وهمية هنا لاختبار السحب
            'message' => 'Mock GET contacts successful'
        ]);
    });

    Route::post('/contacts', function (Request $request) {
        return response()->json([
            'success' => true,
            'contact_id' => 'central_' . uniqid(), // توليد معرّف وهمي
            'message' => 'Mock POST contacts successful'
        ]);
    });

    Route::put('/contacts/{id}', function (Request $request, $id) {
        return response()->json([
            'success' => true,
            'message' => 'Mock PUT contacts successful'
        ]);
    });
});

// مسار المستخدم (محمي بـ Sanctum)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
