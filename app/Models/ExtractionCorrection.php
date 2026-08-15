<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * سجل قرارات المراجعة اليدوية (موافقة/رفض) على أرقام استُخرجت من مصدر منخفض الثقة، تُستخدم
 * لتفعيل "الثقة المكتسبة" التلقائية — راجع MonitorFolderCommand::isLearnedTrusted().
 */
class ExtractionCorrection extends Model
{
    protected $fillable = [
        'phone_number',
        'source',
        'decision',
        'message_id',
        'source_filename',
    ];
}
