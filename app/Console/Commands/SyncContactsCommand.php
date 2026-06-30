<?php

namespace App\Console\Commands;

use App\Jobs\SyncContactsJob;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * أمر Artisan لمزامنة جهات الاتصال مع النظام المركزي
 *
 * الاستخدام:
 *   php artisan contacts:sync           -- مزامنة كل المستخدمين
 *   php artisan contacts:sync --user=1  -- مزامنة مستخدم محدد
 *   php artisan contacts:sync --now     -- تنفيذ فوري بدون Queue
 */
class SyncContactsCommand extends Command
{
    protected $signature = 'contacts:sync
                            {--user= : معرّف المستخدم لمزامنة جهات اتصاله فقط}
                            {--now : تنفيذ فوري بدون Queue}';

    protected $description = 'مزامنة جهات الاتصال مع النظام المركزي';

    public function handle(): int
    {
        $userId = $this->option('user');
        $immediate = $this->option('now');

        if ($userId) {
            // مزامنة مستخدم محدد
            $this->syncUser((int) $userId, $immediate);
        } else {
            // مزامنة كل المستخدمين الذين لديهم جهات تحتاج مزامنة
            $userIds = Contact::needsSync()
                ->distinct()
                ->pluck('user_id');

            if ($userIds->isEmpty()) {
                $this->info('✅ لا توجد جهات اتصال تحتاج مزامنة');
                return 0;
            }

            $this->info("🔄 بدء المزامنة لـ {$userIds->count()} مستخدم...");

            foreach ($userIds as $id) {
                $this->syncUser($id, $immediate);
            }
        }

        $this->info('✅ تم إرسال مهام المزامنة بنجاح');
        return 0;
    }

    /**
     * مزامنة مستخدم واحد
     */
    private function syncUser(int $userId, bool $immediate): void
    {
        $pendingCount = Contact::forUser($userId)->needsSync()->count();

        if ($pendingCount === 0) {
            $this->line("  ⏭️  المستخدم #{$userId}: لا توجد جهات تحتاج مزامنة");
            return;
        }

        $this->line("  📤 المستخدم #{$userId}: {$pendingCount} جهة اتصال بانتظار المزامنة");

        if ($immediate) {
            // تنفيذ فوري
            $syncService = app(\App\Services\ContactSyncService::class);
            $results = $syncService->syncForUser($userId);

            $this->line("     ✅ رُفعت: {$results['uploaded']} | حُدّثت: {$results['updated_remote']} | سُحبت: {$results['downloaded']} | فشلت: {$results['failed']}");
        } else {
            // إرسال إلى Queue
            SyncContactsJob::dispatch($userId);
            $this->line("     📨 تم إرسال مهمة المزامنة إلى Queue");
        }
    }
}
