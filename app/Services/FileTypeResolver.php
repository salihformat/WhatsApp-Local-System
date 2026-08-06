<?php

namespace App\Services;

/**
 * يحدد امتداد الملف الفعلي من عدة مصادر بترتيب موثوقيتها، لأن أي مصدر منها بمفرده قد يكون غائباً:
 * اسم الملف (غير متوفر لصور واتساب إطلاقاً — بخلاف المستندات)، رابط التخزين (روابط S3 المُرحَّلة
 * تستخدم مُعرِّفاً عشوائياً بلا امتداد)، ثم نوع MIME كحل أخير موثوق (متوفر دائماً تقريباً).
 * يُستخدم من PrintRuleEngine (لتحديد إمكانية الطباعة) وProcessPrintJob (لتسمية الملف عند تنزيله)
 * حتى تبقى النتيجة متسقة بين الجهتين ولا تنحرف إحداهما عن الأخرى بمرور الوقت.
 */
class FileTypeResolver
{
    private const MIME_TO_EXTENSION = [
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/bmp' => 'bmp',
        'image/tiff' => 'tiff',
        'application/pdf' => 'pdf',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
        'application/vnd.ms-powerpoint' => 'ppt',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
    ];

    /**
     * @param string|null $fileName اسم الملف الأصلي إن وُجد
     * @param string|null $urlOrPath رابط بعيد أو مسار محلي للملف
     * @param string|null $mimeType نوع MIME (مثال: "image/jpeg")
     * @param string $fallback الامتداد الافتراضي إن تعذّر تحديده من كل المصادر
     */
    public static function resolveExtension(?string $fileName, ?string $urlOrPath, ?string $mimeType, string $fallback = ''): string
    {
        if (!empty($fileName)) {
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if ($extension !== '') {
                return $extension;
            }
        }

        if (!empty($urlOrPath)) {
            $path = parse_url($urlOrPath, PHP_URL_PATH) ?: $urlOrPath;
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if ($extension !== '') {
                return $extension;
            }
        }

        $mimeType = strtolower(trim((string) $mimeType));
        if (isset(self::MIME_TO_EXTENSION[$mimeType])) {
            return self::MIME_TO_EXTENSION[$mimeType];
        }

        return $fallback;
    }
}
