<?php

namespace App\Services;

use App\Models\Message;

/**
 * مطابقة ملف فعلي على القرص داخل أحد مجلدات PrintMonitor (review/processing/archive/failed) مع سجل
 * الرسالة المقابل له — مستخدَمة من صفحة متابعة الإرسال (لعرض رقم الجوال/سبب الفشل) ومن أمر تنظيف
 * الملفات اليتيمة (printmonitor:cleanup-orphans)، استُخرجت هنا لتفادي تكرار نفس منطق المطابقة.
 */
class PrintMonitorFileMatcher
{
    /**
     * حالات الرسالة المتوقعة لكل مجلد فعلي على القرص — تُستخدم لفكّ التلبّس عندما يُعاد إرسال نفس اسم
     * الملف أكثر من مرة عبر الزمن (مثلاً نفس الفاتورة تُطبع كل شهر بنفس الاسم)، فيصبح هناك أكثر من
     * سجل Message بنفس source_filename/file_name. بدون هذا الفلتر، قد تُختار الأحدث إنشاءً بصرف النظر
     * عن حالتها — فقد يظهر ملف في مجلد "فشلت" لكن السجل المطابق له فعلياً (الأحدث) هو محاولة لاحقة نجحت.
     */
    public const FOLDER_EXPECTED_STATUSES = [
        'archive' => ['sent', 'delivered', 'read'],
        'failed' => ['failed', 'no_whatsapp'],
        'processing' => ['processing', 'pending'],
        'review' => ['review_pending'],
    ];

    /**
     * مطابقة اسم ملف فعلي على القرص مع سجل الرسالة المقابل له عبر source_filename أولاً، ثم file_name
     * كخيار احتياطي للسجلات القديمة. نُقيّد البحث بالحالة المتوقعة لهذا المجلد أولاً (راجع
     * FOLDER_EXPECTED_STATUSES)، ونرجع لمطابقة غير مقيّدة إن لم نجد تطابقاً بالحالة المتوقعة.
     */
    public function findMessageForFile(string $filename, ?string $folderKey = null): ?Message
    {
        $base = fn () => Message::where(function ($q) use ($filename) {
            $q->where('source_filename', $filename)->orWhere('file_name', $filename);
        });

        $expectedStatuses = self::FOLDER_EXPECTED_STATUSES[$folderKey] ?? null;
        if ($expectedStatuses) {
            $match = $base()->whereIn('status', $expectedStatuses)->orderByDesc('created_at')->first();
            if ($match) {
                return $match;
            }
        }

        return $base()->orderByDesc('created_at')->first();
    }
}
