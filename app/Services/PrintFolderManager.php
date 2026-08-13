<?php

namespace App\Services;

use App\Models\PrintJob;
use App\Models\Printer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * يدير مجلداً فرعياً مستقلاً لكل طابعة داخل مجلد المراقبة الرئيسي، مخصص للطباعة المحلية المباشرة
 * (بلا أي علاقة بواتساب): C:\PrintMonitor\print\<اسم الطابعة>\ — يضع المستخدم الملف مباشرة في مجلد
 * الطابعة المطلوبة، فيُطبع تلقائياً أو ينتظر موافقة حسب Printer::print_mode (نفس آلية الطباعة عبر
 * واتساب تماماً). كل طابعة تملك أربعة مجلدات فرعية: الجذر (صندوق وارد)، processing، archive، failed.
 */
class PrintFolderManager
{
    public function baseFolder(): string
    {
        return rtrim(config('app.monitor_folder_path', 'C:/PrintMonitor'), '/\\') . '/print';
    }

    /**
     * مسار مجلد الوارد الخاص بطابعة معيّنة (بلا إنشائه فعلياً) — للعرض في الواجهة فقط.
     */
    public function printerFolderPath(Printer $printer): string
    {
        return $this->baseFolder() . '/' . $this->sanitizeFolderName($printer->name);
    }

    /**
     * @return array{inbox: string, processing: string, archive: string, failed: string}
     */
    public function ensureFolders(Printer $printer): array
    {
        $root = $this->printerFolderPath($printer);

        $paths = [
            'inbox' => $root,
            'processing' => $root . '/processing',
            'archive' => $root . '/archive',
            'failed' => $root . '/failed',
        ];

        foreach ($paths as $path) {
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
        }

        return $paths;
    }

    /**
     * ينقل الملف الأصلي (المحفوظ أثناء الانتظار في processing) إلى archive بعد نجاح الطباعة فعلياً.
     */
    public function moveToArchive(PrintJob $printJob): void
    {
        $this->moveSourceFile($printJob, 'archive');
    }

    /**
     * ينقل الملف الأصلي إلى failed بعد فشل الطباعة نهائياً أو رفض الموافقة عليها.
     */
    public function moveToFailed(PrintJob $printJob): void
    {
        $this->moveSourceFile($printJob, 'failed');
    }

    private function moveSourceFile(PrintJob $printJob, string $targetFolder): void
    {
        if (empty($printJob->source_file_path) || !File::exists($printJob->source_file_path)) {
            return;
        }

        if (!$printJob->printer) {
            return;
        }

        try {
            $paths = $this->ensureFolders($printJob->printer);
            $target = $paths[$targetFolder] . '/' . basename($printJob->source_file_path);

            if (File::exists($target)) {
                File::delete($printJob->source_file_path);
            } else {
                File::move($printJob->source_file_path, $target);
            }

            // [Fix] كان العمود يبقى مشيراً للمسار القديم في processing/ رغم أن الملف انتقل فعلياً —
            // يُكسر أي استخدام لاحق له (مثل معاينة الملف بعد اكتمال الطباعة) لأن الملف لم يعد هناك.
            $printJob->update(['source_file_path' => $target]);
        } catch (\Exception $e) {
            Log::error("PrintFolderManager: failed to move source file for PrintJob {$printJob->id} to {$targetFolder}: " . $e->getMessage());
        }
    }

    /**
     * اسم مجلد آمن لنظام ملفات Windows: يستبدل الأحرف المحظورة (\ / : * ? " < > |) بشرطة سفلية.
     */
    private function sanitizeFolderName(string $name): string
    {
        $safe = preg_replace('/[\\\\\/:*?"<>|]+/', '_', trim($name));
        $safe = trim($safe, ' _');

        return $safe !== '' ? $safe : 'طابعة';
    }
}
