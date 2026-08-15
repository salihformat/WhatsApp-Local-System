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
            ->logOnly(['name', 'windows_printer_name', 'type', 'print_mode', 'is_default', 'is_active', 'notes', 'supports_status_check'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('printers');
    }

    protected $fillable = [
        'name',
        'windows_printer_name',
        'type',
        'print_mode',
        'is_default',
        'is_active',
        'notes',
        'supports_status_check',
        'last_status',
        'last_status_healthy',
        'last_status_detail',
        'last_checked_at',
        'pages_printed',
        'fallback_printer_id',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'supports_status_check' => 'boolean',
        'last_status_healthy' => 'boolean',
        'last_checked_at' => 'datetime',
        'pages_printed' => 'integer',
    ];

    public function fallbackPrinter()
    {
        return $this->belongsTo(Printer::class, 'fallback_printer_id');
    }

    /**
     * سليمة إن لم تُفحص بعد بعد (بلا خطأ) أو كان آخر فحص فعلي سليماً — الطابعات الجديدة غير المفحوصة
     * لا يجب اعتبارها "معطّلة" وتحويل مهامها فوراً لطابعة احتياطية عشوائياً.
     */
    public function isHealthy(): bool
    {
        return $this->last_checked_at === null || $this->last_status_healthy;
    }

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

    public function needsApproval(): bool
    {
        return $this->print_mode === 'approval';
    }
}
