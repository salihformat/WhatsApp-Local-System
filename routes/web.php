<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactImportController;

// Authentication Routes
require __DIR__.'/auth.php';
// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications - جرس الإشعارات في الشريط العلوي
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    // Messages
    Route::resource('messages', MessageController::class)->except(['edit']);

    // Conversations - المحادثات
    Route::resource('conversations', \App\Http\Controllers\ConversationController::class)->only(['index', 'show']);
    Route::post('/conversations/{conversation}/close', [\App\Http\Controllers\ConversationController::class, 'close'])->name('conversations.close');
    Route::post('/conversations/{conversation}/reopen', [\App\Http\Controllers\ConversationController::class, 'reopen'])->name('conversations.reopen');
    Route::post('/conversations/{conversation}/assign', [\App\Http\Controllers\ConversationController::class, 'assign'])->name('conversations.assign');
    Route::post('/conversations/{conversation}/messages', [\App\Http\Controllers\ConversationController::class, 'storeMessage'])->name('conversations.messages.store');
    Route::get('/conversations/{conversation}/messages/fetch', [\App\Http\Controllers\ConversationController::class, 'fetchMessages'])->name('conversations.messages.fetch');
    
    // Internal Notes - الملاحظات الداخلية
    Route::post('/conversations/{conversation}/notes', [\App\Http\Controllers\InternalNoteController::class, 'store'])->name('conversations.notes.store');
    Route::delete('/conversations/{conversation}/notes/{note}', [\App\Http\Controllers\InternalNoteController::class, 'destroy'])->name('conversations.notes.destroy');

    // Contacts - جهات الاتصال
    Route::resource('contacts', ContactController::class);
    Route::post('/contacts/bulk-actions', [ContactController::class, 'bulkActions'])->name('contacts.bulk-actions');
    Route::post('/contacts/{contact}/toggle-favorite', [ContactController::class, 'toggleFavorite'])->name('contacts.toggle-favorite');
    Route::get('/api/contacts/search', [ContactController::class, 'search'])->name('contacts.search');
    Route::post('/contacts/sync', [ContactController::class, 'syncNow'])->name('contacts.sync')->middleware('throttle:5,1');

    // Contact Groups - المجموعات
    Route::get('/contacts-groups', [ContactController::class, 'groups'])->name('contacts.groups.index');
    Route::post('/contacts-groups', [ContactController::class, 'storeGroup'])->name('contacts.groups.store');
    Route::put('/contacts-groups/{group}', [ContactController::class, 'updateGroup'])->name('contacts.groups.update');
    Route::delete('/contacts-groups/{group}', [ContactController::class, 'destroyGroup'])->name('contacts.groups.destroy');

    // Contact Import - الاستيراد
    Route::get('/contacts-import', [ContactImportController::class, 'index'])->name('contacts.import.index');
    Route::post('/contacts-import/upload', [ContactImportController::class, 'upload'])->name('contacts.import.upload');
    Route::post('/contacts-import/process', [ContactImportController::class, 'process'])->name('contacts.import.process');
    Route::get('/contacts-import/template', [ContactImportController::class, 'downloadTemplate'])->name('contacts.import.template');

    // أدوات PDF - متاحة لكل المستخدمين المسجّلين (أداة مساعدة، ليست إدارية)
    Route::get('/pdf-tools', [\App\Http\Controllers\PdfToolController::class, 'index'])->name('pdf-tools.index');
    Route::post('/pdf-tools/merge', [\App\Http\Controllers\PdfToolController::class, 'merge'])->name('pdf-tools.merge');
    Route::post('/pdf-tools/split', [\App\Http\Controllers\PdfToolController::class, 'split'])->name('pdf-tools.split');
    Route::post('/pdf-tools/compress-image', [\App\Http\Controllers\PdfToolController::class, 'compressImage'])->name('pdf-tools.compress-image');
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
    Route::post('/api/messages/local-statuses', function(\Illuminate\Http\Request $request) {
        $ids = $request->input('ids', []);
        if (empty($ids)) return response()->json([]);
        
        $messages = \App\Models\Message::whereIn('id', $ids)->get(['id', 'status', 'error_message', 'sent_at', 'delivered_at', 'read_at']);
        
        return response()->json($messages->keyBy('id'));
    })->name('messages.local-statuses');

    // Users, Settings and Service Control (Admin Only)
    Route::middleware(['admin'])->group(function () {
        // Dashboard service controls - can disrupt the whole company's message queue, admin-only
        Route::post('/dashboard/scan', [DashboardController::class, 'scanFolder'])->name('dashboard.scan');
        Route::post('/dashboard/retry-failed', [DashboardController::class, 'retryAllFailed'])->name('dashboard.retry-failed');
        Route::get('/dashboard/check-connection', [DashboardController::class, 'checkConnection'])->name('dashboard.check-connection');
        Route::post('/dashboard/process-queue', [DashboardController::class, 'processQueue'])->name('dashboard.process-queue');
        Route::post('/dashboard/start-services', [DashboardController::class, 'startServices'])->name('dashboard.start-services');
        Route::post('/dashboard/stop-services', [DashboardController::class, 'stopServices'])->name('dashboard.stop-services');
        Route::post('/dashboard/restart-queue', [DashboardController::class, 'restartQueue'])->name('dashboard.restart-queue');

        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle-availability', [UserController::class, 'toggleAvailability'])->name('users.toggle-availability');

        // Settings
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        // Reports
        Route::get('/reports/performance', [\App\Http\Controllers\ReportController::class, 'performance'])->name('reports.performance');

        // Smart Printing - الطباعة الذكية
        Route::get('/printers', [\App\Http\Controllers\PrinterController::class, 'index'])->name('printers.index');
        Route::post('/printers', [\App\Http\Controllers\PrinterController::class, 'store'])->name('printers.store');
        Route::put('/printers/{printer}', [\App\Http\Controllers\PrinterController::class, 'update'])->name('printers.update');
        Route::delete('/printers/{printer}', [\App\Http\Controllers\PrinterController::class, 'destroy'])->name('printers.destroy');
        Route::post('/printers/{printer}/check-now', [\App\Http\Controllers\PrinterController::class, 'checkNow'])->name('printers.check-now');

        // سجل التدقيق - Audit Log
        Route::get('/audit-log', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit-log.index');

        // لوحة صحة النظام - System Health
        Route::get('/system-health', [\App\Http\Controllers\SystemHealthController::class, 'index'])->name('system-health.index');
        Route::post('/system-health/restart-queue', [\App\Http\Controllers\SystemHealthController::class, 'restartQueue'])->name('system-health.restart-queue');
        Route::post('/system-health/clear-queue', [\App\Http\Controllers\SystemHealthController::class, 'clearQueue'])->name('system-health.clear-queue');
        
        // المهام الفاشلة - Failed Jobs
        Route::get('/failed-jobs', [\App\Http\Controllers\FailedJobController::class, 'index'])->name('failed-jobs.index');
        Route::post('/failed-jobs/retry-all', [\App\Http\Controllers\FailedJobController::class, 'retryAll'])->name('failed-jobs.retry-all');
        Route::post('/failed-jobs/flush', [\App\Http\Controllers\FailedJobController::class, 'flush'])->name('failed-jobs.flush');
        Route::post('/failed-jobs/{id}/retry', [\App\Http\Controllers\FailedJobController::class, 'retry'])->name('failed-jobs.retry');
        Route::delete('/failed-jobs/{id}/forget', [\App\Http\Controllers\FailedJobController::class, 'forget'])->name('failed-jobs.forget');

        // متابعة مجلد المراقبة PrintMonitor
        Route::get('/print-monitor', [\App\Http\Controllers\PrintMonitorController::class, 'index'])->name('print-monitor.index');
        Route::post('/print-monitor/approve-all', [\App\Http\Controllers\PrintMonitorController::class, 'approveAll'])->name('print-monitor.approve-all');
        Route::post('/print-monitor/{message}/approve', [\App\Http\Controllers\PrintMonitorController::class, 'approve'])->name('print-monitor.approve');
        Route::post('/print-monitor/{message}/reject', [\App\Http\Controllers\PrintMonitorController::class, 'reject'])->name('print-monitor.reject');
        Route::post('/print-monitor/{message}/set-phone-and-approve', [\App\Http\Controllers\PrintMonitorController::class, 'setPhoneAndApprove'])->name('print-monitor.set-phone-and-approve');

        // محرك الأتمتة العام - Automation Rules
        Route::get('/automation-rules', [\App\Http\Controllers\AutomationRuleController::class, 'index'])->name('automation-rules.index');
        Route::post('/automation-rules', [\App\Http\Controllers\AutomationRuleController::class, 'store'])->name('automation-rules.store');
        Route::put('/automation-rules/{automationRule}', [\App\Http\Controllers\AutomationRuleController::class, 'update'])->name('automation-rules.update');
        Route::delete('/automation-rules/{automationRule}', [\App\Http\Controllers\AutomationRuleController::class, 'destroy'])->name('automation-rules.destroy');

        Route::get('/print-rules', [\App\Http\Controllers\PrintRuleController::class, 'index'])->name('print-rules.index');
        Route::post('/print-rules', [\App\Http\Controllers\PrintRuleController::class, 'store'])->name('print-rules.store');
        Route::put('/print-rules/{printRule}', [\App\Http\Controllers\PrintRuleController::class, 'update'])->name('print-rules.update');
        Route::delete('/print-rules/{printRule}', [\App\Http\Controllers\PrintRuleController::class, 'destroy'])->name('print-rules.destroy');

        Route::get('/print-jobs', [\App\Http\Controllers\PrintJobController::class, 'index'])->name('print-jobs.index');
        Route::post('/print-jobs/approve-all', [\App\Http\Controllers\PrintJobController::class, 'approveAll'])->name('print-jobs.approve-all');
        Route::post('/print-jobs/reject-all', [\App\Http\Controllers\PrintJobController::class, 'rejectAll'])->name('print-jobs.reject-all');
        Route::post('/print-jobs/{printJob}/retry', [\App\Http\Controllers\PrintJobController::class, 'retry'])->name('print-jobs.retry');
        Route::post('/print-jobs/{printJob}/approve', [\App\Http\Controllers\PrintJobController::class, 'approve'])->name('print-jobs.approve');
        Route::post('/print-jobs/{printJob}/reject', [\App\Http\Controllers\PrintJobController::class, 'reject'])->name('print-jobs.reject');
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


