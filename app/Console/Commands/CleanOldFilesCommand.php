<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
    protected $description = 'Clean old files from archive, failed, processing, review and print_jobs directories based on FILE_AUTO_DELETE_DAYS setting';

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
            // [Fix] ملفات بانتظار المراجعة اليدوية (review) لم تكن تُنظَّف إطلاقاً — قد تتراكم إلى ما لا
            // نهاية إن لم يُراجعها أحد. عند حذفها هنا، نُحدّث حالة رسالتها المرتبطة إلى "فشلت" أيضاً
            // (بدل تركها "بانتظار المراجعة" في قاعدة البيانات بلا ملف فعلي يدعمها) حتى تبقى صفحة
            // متابعة الإرسال متسقة مع ما هو موجود فعلياً على القرص.
            $folderPath . '/review',
            // [Fix 2026-08-06] النسخ المحلية من ملفات الطباعة (print_jobs) لم تكن تُنظَّف إطلاقاً —
            // تتراكم إلى الأبد لأنها لا علاقة لها بمجلد المراقبة PrintMonitor. تُستخدم نفس مدة
            // الاحتفاظ (FILE_AUTO_DELETE_DAYS) لتوحيد سياسة الحذف بدل إعداد منفصل.
            Storage::disk('local')->path(config('printing.download_path', 'print_jobs')),
        ];

        $now = now();
        $deletedCount = 0;

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                continue;
            }

            $isReviewFolder = rtrim(str_replace('\\', '/', $directory), '/') === rtrim(str_replace('\\', '/', $folderPath . '/review'), '/');
            $files = File::allFiles($directory);

            foreach ($files as $file) {
                $lastModified = \Carbon\Carbon::createFromTimestamp($file->getMTime());

                // [Fix] $now->diffInDays($lastModified) كان يُعيد قيمة سالبة دائماً (لأن $lastModified
                // في الماضي بالنسبة لـ $now في اتجاه هذا الاستدعاء تحديداً في نسخة Carbon المستخدمة)،
                // فالشرط `>= $days` لم يتحقق أبداً مهما قدُم الملف — هذا يعني عملياً أن هذا الأمر لم يحذف
                // أي ملف إطلاقاً منذ إضافته. الاتجاه الصحيح: $lastModified->diffInDays($now) (كم يوماً
                // مضى منذ آخر تعديل حتى الآن)، ويُعيد قيمة موجبة صحيحة.
                if ($lastModified->diffInDays($now) >= $days) {
                    $filename = $file->getFilename();

                    try {
                        File::delete($file->getRealPath());
                        $deletedCount++;
                        Log::info("Auto-deleted old file: " . $filename);

                        if ($isReviewFolder) {
                            $expiredCount = Message::where('status', 'review_pending')
                                ->where(function ($q) use ($filename) {
                                    $q->where('source_filename', $filename)->orWhere('file_name', $filename);
                                })
                                ->update([
                                    'status' => 'failed',
                                    'error_message' => "انتهت مهلة المراجعة اليدوية ({$days} يوم) بلا رد، وتم حذف الملف تلقائياً.",
                                ]);

                            if ($expiredCount > 0) {
                                Log::info("Marked {$expiredCount} review_pending message(s) as expired/failed for deleted file: {$filename}");
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error("Failed to delete old file " . $filename . ": " . $e->getMessage());
                    }
                }
            }
        }

        $this->info("Successfully deleted {$deletedCount} old files from archive/failed/processing/review/print_jobs folders.");
        return Command::SUCCESS;
    }
}
