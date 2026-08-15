<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * سجل تتبّع عملية استخراج رقم الجوال/رقم الملف من اسم كل ملف أو محتواه أثناء مسح مجلد المراقبة،
 * يُستخدم لعرض "لماذا اتخذ النظام هذا القرار" في صفحة متابعة الإرسال دون الحاجة لقراءة اللوج يدوياً.
 */
class ExtractionTrace extends Model
{
    protected $fillable = [
        'filename',
        'extension',
        'source',
        'matched_label',
        'file_number',
        'contact_id',
        'excluded',
        'final_phone',
        'rtl_corrected',
        'pdf_ocr_used',
        'learned_trusted',
    ];

    protected $casts = [
        'excluded' => 'array',
        'rtl_corrected' => 'boolean',
        'pdf_ocr_used' => 'boolean',
        'learned_trusted' => 'boolean',
    ];
}
