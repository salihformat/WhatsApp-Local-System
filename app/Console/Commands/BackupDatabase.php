<?php

namespace App\Console\Commands;

use App\Services\AdminNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;

/**
 * نسخ احتياطي يومي مجدول لقاعدة البيانات (mysqldump) — النظام يعتمد بالكامل على قاعدة بيانات
 * محلية واحدة (رسائل، مهام طباعة، جهات اتصال...)، فأي عطل في القرص أو الجهاز بلا نسخة احتياطية
 * يعني فقدان كل السجل التاريخي نهائياً. يُخزَّن كل نسخة مضغوطة (gzip) في storage/app/backups
 * (قرص محلي خاص، خارج المجلد العام public)، مع حذف النسخ الأقدم من app.backup_retention_days
 * تلقائياً لمنع امتلاء القرص بمرور الوقت.
 */
class BackupDatabase extends Command
{
    protected $signature = 'backup:database';

    protected $description = 'Create a compressed mysqldump backup of the database and clean up old backups';

    public function handle(): int
    {
        $dumpBinary = env('MYSQLDUMP_PATH', 'C:/xampp/mysql/bin/mysqldump.exe');
        if (!file_exists($dumpBinary)) {
            $error = "mysqldump غير موجود في المسار: {$dumpBinary} — اضبط MYSQLDUMP_PATH في .env";
            $this->error($error);
            Log::error("BackupDatabase: {$error}");
            app(AdminNotifier::class)->notify("⚠️ فشل النسخ الاحتياطي التلقائي لقاعدة البيانات\nالسبب: {$error}", ['source' => 'backup_failure']);
            return self::FAILURE;
        }

        $backupDir = storage_path('app/backups');
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $database = config('database.connections.mysql.database');
        $filename = 'backup_' . $database . '_' . now()->format('Y-m-d_His') . '.sql.gz';
        $path = $backupDir . '/' . $filename;

        $this->info("Backing up database '{$database}' to {$filename}...");

        $result = Process::timeout(300)->run([
            $dumpBinary,
            '--host=' . config('database.connections.mysql.host'),
            '--port=' . config('database.connections.mysql.port'),
            '--user=' . config('database.connections.mysql.username'),
            '--password=' . config('database.connections.mysql.password'),
            '--single-transaction',
            '--quick',
            '--routines',
            $database,
        ]);

        if (!$result->successful()) {
            $error = trim($result->errorOutput() ?: 'mysqldump exited with error');
            $this->error("Backup failed: {$error}");
            Log::error('BackupDatabase: mysqldump failed', ['error' => $error]);
            app(AdminNotifier::class)->notify("⚠️ فشل النسخ الاحتياطي التلقائي لقاعدة البيانات\nالسبب: {$error}", ['source' => 'backup_failure']);
            return self::FAILURE;
        }

        // ضغط مباشر عبر PHP (gzip) بدل الاعتماد على أداة سطر أوامر خارجية غير مضمون وجودها على Windows
        File::put($path, gzencode($result->output(), 9));

        $sizeMb = round(filesize($path) / (1024 * 1024), 2);
        $this->info("✅ Backup created: {$filename} ({$sizeMb} MB)");
        Log::info('BackupDatabase: backup created', ['file' => $filename, 'size_mb' => $sizeMb]);

        $this->cleanupOldBackups($backupDir);

        return self::SUCCESS;
    }

    /**
     * حذف أي نسخة احتياطية أقدم من app.backup_retention_days (نفس اتفاقية files:clean-old
     * المستخدمة لتنظيف مجلد المراقبة)، لمنع امتلاء القرص بنسخ لا نهاية لها بمرور الوقت.
     */
    private function cleanupOldBackups(string $backupDir): void
    {
        $retentionDays = (int) config('app.backup_retention_days', 14);
        if ($retentionDays <= 0) {
            return;
        }

        $cutoff = now()->subDays($retentionDays)->timestamp;
        $deleted = 0;

        foreach (File::files($backupDir) as $file) {
            if ($file->getMTime() < $cutoff) {
                File::delete($file->getPathname());
                $deleted++;
            }
        }

        if ($deleted > 0) {
            $this->info("Cleaned up {$deleted} backup(s) older than {$retentionDays} days.");
        }
    }
}
