<?php

namespace App\Jobs;

use App\Services\ContactSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Job لمزامنة جهات الاتصال مع النظام المركزي في الخلفية
 *
 * يتم تشغيله:
 * - تلقائياً عبر الجدولة (Scheduler) كل 15 دقيقة
 * - يدوياً عبر أمر Artisan: php artisan contacts:sync
 * - عند إضافة/تعديل جهة اتصال (اختياري)
 */
class SyncContactsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var int معرّف المستخدم المراد مزامنة جهات اتصاله */
    private int $userId;

    /** @var int عدد المحاولات القصوى */
    public int $tries = 3;

    /** @var int المهلة الزمنية بالثواني */
    public int $timeout = 300;

    /** @var int التأخير بين المحاولات (ثواني) */
    public int $backoff = 30;

    /**
     * @param int $userId معرّف المستخدم
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
        $this->onQueue('contacts-sync');
    }

    /**
     * تنفيذ المزامنة
     */
    public function handle(ContactSyncService $syncService): void
    {
        Log::info('SyncContactsJob: Starting sync', ['user_id' => $this->userId]);

        try {
            $results = $syncService->syncForUser($this->userId);

            Log::info('SyncContactsJob: Sync completed', [
                'user_id' => $this->userId,
                'uploaded' => $results['uploaded'],
                'updated_remote' => $results['updated_remote'],
                'downloaded' => $results['downloaded'],
                'updated_local' => $results['updated_local'],
                'failed' => $results['failed'],
            ]);

        } catch (Exception $e) {
            Log::error('SyncContactsJob: Sync failed', [
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // إعادة الرمي حتى تعمل آلية إعادة المحاولة
            throw $e;
        }
    }

    /**
     * معالجة فشل الـ Job بعد استنفاد المحاولات
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SyncContactsJob: All retries exhausted', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}
