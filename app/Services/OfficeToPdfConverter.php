<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

/**
 * يحوّل ملفات Word/Excel/PowerPoint إلى PDF عبر LibreOffice بوضع headless (بلا واجهة) قبل الطباعة،
 * لأن SumatraPDF لا يدعم هذه الصيغ مباشرة (بعكس PDF والصور).
 */
class OfficeToPdfConverter
{
    public function convert(string $inputPath): string
    {
        $sofficePath = config('printing.libreoffice_path');
        if (!file_exists($sofficePath)) {
            throw new Exception("LibreOffice غير موجود في: {$sofficePath}. يجب تثبيته (راجع دليل التشغيل).");
        }

        if (!file_exists($inputPath)) {
            throw new Exception("الملف المطلوب تحويله غير موجود: {$inputPath}");
        }

        $outputDir = dirname($inputPath);
        $expectedOutputPath = $outputDir . DIRECTORY_SEPARATOR . pathinfo($inputPath, PATHINFO_FILENAME) . '.pdf';

        // [Fix 2026-08-06] لوحظ فعلياً: محاولة تحويل سابقة انتهت بتجاوز المهلة (تعليق حقيقي) تركت
        // ملف قفل ".~lock.<الاسم>#" (تُنشئه LibreOffice نفسها عند بدء الكتابة) بقي عالقاً في مجلد
        // الإخراج، فتسبب بفشل كل محاولة لاحقة بخطأ IO غامض (SfxBaseModel::impl_store ... Abort) رغم
        // نجاح نفس الأمر تماماً عند تغيير مجلد الإخراج فقط — نُنظّف أي قفل/ناتج جزئي عالق قبل كل محاولة.
        $staleLockFile = $outputDir . DIRECTORY_SEPARATOR . '.~lock.' . basename($expectedOutputPath) . '#';
        if (file_exists($staleLockFile)) {
            @unlink($staleLockFile);
        }
        if (file_exists($expectedOutputPath)) {
            @unlink($expectedOutputPath);
        }

        // -env:UserInstallation بملف شخصي (profile) فريد لكل تحويل — بلا هذا، تشغيل أكثر من تحويل
        // في نفس اللحظة (أو خلال ثوانٍ من تحويل سابق لم يُغلق ملفه الشخصي بعد) يفشل بخطأ قفل الملف
        // الشخصي المشترك الافتراضي لدى LibreOffice.
        $profileDir = storage_path('app/private/libreoffice_profiles/' . Str::uuid());

        try {
            $result = Process::timeout(config('printing.office_conversion_timeout', 120))->run([
                $sofficePath,
                '--headless',
                '--norestore',
                '-env:UserInstallation=file:///' . str_replace('\\', '/', $profileDir),
                '--convert-to', 'pdf',
                '--outdir', $outputDir,
                $inputPath,
            ]);

            if (!$result->successful()) {
                throw new Exception('فشل تحويل ملف الأوفيس إلى PDF: ' . trim($result->errorOutput() ?: $result->output()));
            }

            if (!file_exists($expectedOutputPath)) {
                throw new Exception('اكتمل أمر LibreOffice بلا خطأ لكن ملف PDF الناتج غير موجود: ' . $expectedOutputPath);
            }

            return $expectedOutputPath;
        } finally {
            if (File::exists($profileDir)) {
                File::deleteDirectory($profileDir);
            }
        }
    }
}
