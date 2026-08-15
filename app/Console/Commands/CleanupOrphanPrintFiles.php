<?php

namespace App\Console\Commands;

use App\Services\PrintMonitorFileMatcher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * تنظيف تلقائي للملفات اليتيمة في مجلد المراقبة C:\PrintMonitor\failed: ملف تبقى فيه منذ محاولة
 * إرسال فاشلة أولى، لكن رسالته أُعيدت جدولتها تلقائياً لاحقاً (راجع Schedule::call في
 * routes/console.php) ونجحت — فلم يعد يقابله أي سجل رسالة بحالة "failed" فعلياً، ويبقى الملف يتيماً
 * في مجلد الفشل بلا أي سبب معروض في /print-monitor (نفس الحالة التي اكتُشفت واقعياً وأُصلح جذرها في
 * SendMessageJob::moveFolderFile — هذا الأمر شبكة أمان إضافية لأي حالة سابقة أو نادرة لم يمسكها ذلك
 * الإصلاح، وللملفات التي بلا أي سجل رسالة إطلاقاً).
 */
class CleanupOrphanPrintFiles extends Command
{
    protected $signature = 'printmonitor:cleanup-orphans {--dry-run : عرض ما سيُنقَل بدون تنفيذ فعلي}';

    protected $description = 'نقل الملفات اليتيمة في مجلد failed (التي نجحت رسالتها لاحقاً) إلى archive، والتنبيه بالملفات بلا أي سجل رسالة';

    public function handle(PrintMonitorFileMatcher $matcher): int
    {
        $monitorFolder = config('app.monitor_folder_path', 'C:/PrintMonitor');
        $failedPath = $monitorFolder . '/failed';
        $archivePath = $monitorFolder . '/archive';
        $dryRun = (bool) $this->option('dry-run');

        if (!File::exists($failedPath)) {
            $this->info('لا يوجد مجلد failed بعد.');
            return self::SUCCESS;
        }

        $relocated = 0;
        $unmatched = 0;

        foreach (File::files($failedPath) as $file) {
            $filename = $file->getFilename();
            if (str_starts_with($filename, '.')) {
                continue;
            }

            $message = $matcher->findMessageForFile($filename, 'failed');

            if (!$message) {
                $unmatched++;
                $this->line("⚠️ لا يوجد أي سجل رسالة لهذا الملف إطلاقاً: {$filename}");
                continue;
            }

            if (in_array($message->status, PrintMonitorFileMatcher::FOLDER_EXPECTED_STATUSES['failed'], true)) {
                // فشل حقيقي وما زال كذلك — هذا مكانه الصحيح، لا شيء لفعله.
                continue;
            }

            if (!in_array($message->status, PrintMonitorFileMatcher::FOLDER_EXPECTED_STATUSES['archive'], true)) {
                // حالة أخرى (pending/processing/review_pending) — قد يكون قيد إعادة محاولة حالياً، تجاهله الآن.
                continue;
            }

            $this->warn("📦 نسخة قديمة: '{$filename}' — رسالة #{$message->id} نجحت لاحقاً (الحالة: {$message->status}). " . ($dryRun ? 'سيُنقَل إلى archive.' : 'جارٍ نقله إلى archive...'));

            if ($dryRun) {
                $relocated++;
                continue;
            }

            if (!File::exists($archivePath)) {
                File::makeDirectory($archivePath, 0755, true);
            }

            $target = $this->uniqueTargetPath($archivePath, $filename);

            try {
                File::move($file->getPathname(), $target);
                Log::info('CleanupOrphanPrintFiles: relocated stale failed-folder file to archive', [
                    'filename' => $filename,
                    'message_id' => $message->id,
                    'target' => $target,
                ]);
                $relocated++;
            } catch (\Exception $e) {
                Log::error("CleanupOrphanPrintFiles: failed to relocate '{$filename}': " . $e->getMessage());
                $this->error("فشل نقل '{$filename}': " . $e->getMessage());
            }
        }

        $this->info("اكتمل الفحص. نُقل: {$relocated}، بلا سجل رسالة: {$unmatched}.");

        return self::SUCCESS;
    }

    /**
     * نفس أسلوب تسمية النسخ عند التعارض المستخدم في SendMessageJob::moveFolderFile — يُبقي الاتساق
     * بين كل نقاط نقل الملفات بين مجلدات PrintMonitor.
     */
    private function uniqueTargetPath(string $dir, string $filename): string
    {
        $target = $dir . '/' . $filename;
        if (!File::exists($target)) {
            return $target;
        }

        $pathInfo = pathinfo($filename);
        $baseName = $pathInfo['filename'];
        $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';

        $counter = 1;
        while (File::exists($dir . '/' . $baseName . '_' . $counter . $extension)) {
            $counter++;
        }

        return $dir . '/' . $baseName . '_' . $counter . $extension;
    }
}
