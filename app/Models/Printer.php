<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Printer extends Model
{
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        // نتجاهل عمود حالة الفحص الدوري (last_status/last_checked_at) عمداً لئلا يُغرق سجل
        // التدقيق بمئات الإدخالات الروتينية كل 10 دقائق — التدقيق هنا لتغييرات الإعداد اليدوية فقط
        return LogOptions::defaults()
            ->logOnly(['name', 'windows_printer_name', 'type', 'is_default', 'is_active', 'notes', 'supports_status_check'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('printers');
    }

    protected $fillable = [
        'name',
        'windows_printer_name',
        'type',
        'is_default',
        'is_active',
        'notes',
        'supports_status_check',
        'last_status',
        'last_status_healthy',
        'last_status_detail',
        'last_checked_at',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'supports_status_check' => 'boolean',
        'last_status_healthy' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    public function rules()
    {
        return $this->hasMany(PrintRule::class);
    }

    public function printJobs()
    {
        return $this->hasMany(PrintJob::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(PrinterStatusLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
