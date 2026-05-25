<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;

// Authentication Routes
require __DIR__.'/auth.php';
// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/scan', [DashboardController::class, 'scanFolder'])->name('dashboard.scan');
    Route::post('/dashboard/retry-failed', [DashboardController::class, 'retryAllFailed'])->name('dashboard.retry-failed');
    Route::post('/dashboard/process-queue', [DashboardController::class, 'processQueue'])->name('dashboard.process-queue');

    // Messages
    Route::resource('messages', MessageController::class)->except(['edit', 'update']);
    Route::post('/messages/bulk-actions', [MessageController::class, 'bulkActions'])->name('messages.bulk-actions');
    Route::post('/messages/{message}/retry', [MessageController::class, 'retry'])->name('messages.retry');
    Route::get('/api/messages/{message}/status', function($id) {
        $message = \App\Models\Message::findOrFail($id);
        
        if ($message->central_message_id) {
            try {
                $centralApiService = app(\App\Services\CentralApiService::class);
                $result = $centralApiService->syncMessageStatuses([$message->central_message_id]);
                
                if (!empty($result['success']) && isset($result['statuses'][$message->central_message_id])) {
                    $statusData = $result['statuses'][$message->central_message_id];
                    $newStatus = $statusData['status'];
                    
                    $updateData = [];
                    if (!empty($statusData['sent_at'])) $updateData['sent_at'] = $statusData['sent_at'];
                    if (!empty($statusData['delivered_at'])) $updateData['delivered_at'] = $statusData['delivered_at'];
                    if (!empty($statusData['read_at'])) $updateData['read_at'] = $statusData['read_at'];
                    if (!empty($statusData['error_message'])) $updateData['error_message'] = $statusData['error_message'];
                    
                    $message->updateStatus($newStatus, $updateData);
                    $message->refresh();
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Live sync error: " . $e->getMessage());
            }
        }

        return response()->json([
            'status' => $message->status,
            'error_message' => $message->error_message,
            'sent_at' => $message->sent_at ? $message->sent_at->format('Y-m-d H:i:s') : null,
            'delivered_at' => $message->delivered_at ? $message->delivered_at->format('Y-m-d H:i:s') : null,
            'read_at' => $message->read_at ? $message->read_at->format('Y-m-d H:i:s') : null,
        ]);
    })->name('messages.status');
    // Users (Admin Only)
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/docs', function () {
    return view('docs');
})->name('docs');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


