<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PrintRule extends Model
{
    use LogsActivity;

    public const MATCH_TYPES = ['phone_number', 'phone_prefix', 'keyword', 'file_type'];

    // print_and_send: طباعة + إرسال معاً (السلوك الافتراضي القديم) | print_only: طباعة بلا إرسال
    // | send_only: إرسال بلا طباعة | save_only: حفظ فقط بلا طباعة ولا إرسال
    // | hold_for_approval: تعليق الإجراءين حتى موافقة يدوية
    public const ACTION_TYPES = ['print_and_send', 'print_only', 'send_only', 'save_only', 'hold_for_approval'];

    // الإجراءات التي تحتاج طابعة فعّالة لتُنفَّذ
    public const PRINTING_ACTION_TYPES = ['print_and_send', 'print_only'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('print_rules');
    }

    protected $fillable = [
        'name',
        'priority',
        'match_type',
        'match_value',
        'action_type',
        'printer_id',
        'is_active',
    ];

    protected $casts = [
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    public function printer()
    {
        return $this->belongsTo(Printer::class);
    }

    public function requiresPrinter(): bool
    {
        return in_array($this->action_type, self::PRINTING_ACTION_TYPES, true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderedByPriority($query)
    {
        return $query->orderBy('priority')->orderBy('id');
    }
}
