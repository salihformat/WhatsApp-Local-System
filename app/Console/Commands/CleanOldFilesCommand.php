<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CleanOldFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:clean-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old files from archive and failed directories based on FILE_AUTO_DELETE_DAYS setting';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = config('app.file_auto_delete_days', 3);
        
        if ($days <= 0) {
            $this->info("Auto-delete is disabled (days <= 0).");
            return Command::SUCCESS;
        }

        $folderPath = config('app.monitor_folder_path', 'C:/PrintMonitor');

        $directories = [
            $folderPath . '/archive',
            $folderPath . '/failed',
            // ملفات عالقة في processing (لم تُنقل بسبب فشل مطابقة الاسم أو حذف يدوي للرسالة) تُعتبر يتيمة
            // إن بقيت أكثر من مدة الاحتفاظ المعتادة، لأن النقل الطبيعي منها يحدث خلال ثوانٍ/دقائق فقط
            $folderPath . '/processing',
        ];

        $now = now();
        $deletedCount = 0;

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                continue;
            }

            $files = File::allFiles($directory);

            foreach ($files as $file) {
                $lastModified = \Carbon\Carbon::createFromTimestamp($file->getMTime());
                
                if ($now->diffInDays($lastModified) >= $days) {
                    try {
                        File::delete($file->getRealPath());
                        $deletedCount++;
                        Log::info("Auto-deleted old file: " . $file->getFilename());
                    } catch (\Exception $e) {
                        Log::error("Failed to delete old file " . $file->getFilename() . ": " . $e->getMessage());
                    }
                }
            }
        }

        $this->info("Successfully deleted {$deletedCount} old files from archive/failed folders.");
        return Command::SUCCESS;
    }
}
