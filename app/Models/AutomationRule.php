<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AutomationRule extends Model
{
    use LogsActivity;

    public const MATCH_TYPES = ['phone_number', 'phone_prefix', 'keyword'];
    public const ACTION_TYPES = ['assign_user', 'internal_note', 'auto_reply'];

    protected $fillable = [
        'name',
        'priority',
        'match_type',
        'match_value',
        'action_type',
        'action_value',
        'is_active',
    ];

    protected $casts = [
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('automation_rules');
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
