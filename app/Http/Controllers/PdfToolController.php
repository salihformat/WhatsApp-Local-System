<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;

/**
 * أدوات سريعة على الملفات قبل إرسالها عبر واتساب: دمج عدة PDF، تقسيم PDF، ضغط صورة.
 * لا تُخزَّن أي نتيجة في قاعدة البيانات — تحويل لحظي عند الطلب وتنزيل مباشر (Stateless).
 */
class PdfToolController extends Controller
{
    public function index()
    {
        return view('pdf-tools.index');
    }

    public function merge(Request $request)
    {
        $request->validate([
            'files' => ['required', 'array', 'min:2'],
            'files.*' => ['required', 'file', 'mimes:pdf', 'max:20480'],
        ], [
            'files.min' => 'يجب اختيار ملفين PDF على الأقل للدمج',
            'files.*.mimes' => 'كل الملفات يجب أن تكون بصيغة PDF',
        ]);

        try {
            $pdf = new Fpdi();

            foreach ($request->file('files') as $file) {
                $pageCount = $pdf->setSourceFile($file->getRealPath());
                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($templateId);
                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }
            }

            $fileName = 'merged_' . now()->format('Ymd_His') . '.pdf';
            return response($pdf->Output('S', $fileName), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'فشل دمج الملفات: ' . $e->getMessage());
        }
    }

    public function split(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:pdf', 'max:20480'],
            'from_page' => ['required', 'integer', 'min:1'],
            'to_page' => ['required', 'integer', 'min:1', 'gte:from_page'],
        ], [
            'file.mimes' => 'الملف يجب أن يكون بصيغة PDF',
            'to_page.gte' => 'رقم الصفحة الأخيرة يجب أن يكون أكبر من أو يساوي الصفحة الأولى',
        ]);

        try {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($request->file('file')->getRealPath());

            $from = min($request->input('from_page'), $pageCount);
            $to = min($request->input('to_page'), $pageCount);

            if ($from > $pageCount) {
                return back()->with('error', "الملف يحتوي {$pageCount} صفحة فقط، النطاق المطلوب غير موجود");
            }

            for ($i = $from; $i <= $to; $i++) {
                $templateId = $pdf->importPage($i);
                $size = $pdf->getTemplateSize($templateId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId);
            }

            $fileName = 'split_' . $from . '-' . $to . '_' . now()->format('Ymd_His') . '.pdf';
            return response($pdf->Output('S', $fileName), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'فشل تقسيم الملف: ' . $e->getMessage());
        }
    }

    public function compressImage(Request $request)
    {
        $request->validate([
            'image' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:20480'],
            'quality' => ['nullable', 'integer', 'min:10', 'max:95'],
        ]);

        try {
            $quality = (int) $request->input('quality', 60);
            $path = $request->file('image')->getRealPath();
            $extension = strtolower($request->file('image')->getClientOriginalExtension());

            $image = match ($extension) {
                'png' => imagecreatefrompng($path),
                default => imagecreatefromjpeg($path),
            };

            if (!$image) {
                throw new \Exception('تعذّرت قراءة الصورة');
            }

            // الحفاظ على الشفافية عند تصدير PNG بعد التحويل لـ JPEG (خلفية بيضاء بدل أسود)
            $width = imagesx($image);
            $height = imagesy($image);
            $output = imagecreatetruecolor($width, $height);
            imagefill($output, 0, 0, imagecolorallocate($output, 255, 255, 255));
            imagecopy($output, $image, 0, 0, 0, 0, $width, $height);

            ob_start();
            imagejpeg($output, null, $quality);
            $data = ob_get_clean();

            imagedestroy($image);
            imagedestroy($output);

            $fileName = 'compressed_' . now()->format('Ymd_His') . '.jpg';
            return response($data, 200, [
                'Content-Type' => 'image/jpeg',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'X-Original-Size' => $request->file('image')->getSize(),
                'X-Compressed-Size' => strlen($data),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'فشل ضغط الصورة: ' . $e->getMessage());
        }
    }
}
