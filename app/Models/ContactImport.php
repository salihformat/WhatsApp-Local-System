<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'file_name',
        'file_path',
        'total_rows',
        'success_count',
        'failed_count',
        'duplicate_count',
        'updated_count',
        'status',
        'error_log',
        'column_mapping',
        'contact_group_id',
        'completed_at',
    ];

    protected $casts = [
        'error_log' => 'array',
        'column_mapping' => 'array',
        'total_rows' => 'integer',
        'success_count' => 'integer',
        'failed_count' => 'integer',
        'duplicate_count' => 'integer',
        'updated_count' => 'integer',
        'completed_at' => 'datetime',
    ];

    // ===== العلاقات =====

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contactGroup()
    {
        return $this->belongsTo(ContactGroup::class);
    }

    // ===== Scopes =====

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ===== Helpers =====

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * نسبة النجاح
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->total_rows === 0) return 0;
        return round(($this->success_count / $this->total_rows) * 100, 1);
    }

    /**
     * ملخص النتائج
     */
    public function getSummaryAttribute(): string
    {
        return "ناجح: {$this->success_count} | فاشل: {$this->failed_count} | مكرر: {$this->duplicate_count} | محدّث: {$this->updated_count}";
    }
}
