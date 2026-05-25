<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'message_text',
        'file_name',
        'file_type',
        'file_size',
        'file_path',
        'message_type',
        'status',
        'central_message_id',
        'sent_at',
        'delivered_at',
        'read_at',
        'retry_count',
        'error_message',
        'metadata',
        'last_retry_at',
    ];

    /**
     * Get the user that owns the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'file_size' => 'integer',
        'retry_count' => 'integer',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'metadata' => 'array',

        'last_retry_at' => 'datetime',

    ];

//    public function hasFile(): bool
//    {
//        return !empty($this->file_name) && !empty($this->file_path);
//    }

    public function isSent(): bool
    {
        return in_array($this->status, ['sent', 'delivered', 'read']);
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'no_whatsapp']);
    }

//    public function canRetry(): bool
//    {
//        return $this->isFailed() && $this->retry_count < config('app.max_retry_attempts', 5);
//    }



//    public function incrementRetryCount(): void
//    {
//        $this->increment('retry_count');
//    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeNeedsRetry($query)
    {
        return $query->where('status', 'failed')
            ->where('retry_count', '<', config('app.max_retry_attempts', 5));
    }


    /**
     * تحديث حالة الرسالة
     */
    public function updateStatus(string $status, array $metadata = []): void
    {
        $currentMetadata = $this->metadata ?? [];
        $newMetadata = array_merge($currentMetadata, $metadata, [
            'status_updated_at' => now()->toISOString()
        ]);

        $updateData = [
            'status' => $status,
            'metadata' => $newMetadata
        ];

        if (isset($metadata['sent_at'])) {
            $updateData['sent_at'] = $metadata['sent_at'];
        } elseif ($status === 'sent' && !$this->sent_at) {
            $updateData['sent_at'] = now();
        }

        if (isset($metadata['delivered_at'])) {
            $updateData['delivered_at'] = $metadata['delivered_at'];
        } elseif ($status === 'delivered' && !$this->delivered_at) {
            $updateData['delivered_at'] = now();
        }

        if (isset($metadata['read_at'])) {
            $updateData['read_at'] = $metadata['read_at'];
        } elseif ($status === 'read' && !$this->read_at) {
            $updateData['read_at'] = now();
        }

        if (isset($metadata['error_message'])) {
            $updateData['error_message'] = $metadata['error_message'];
        }

        $this->update($updateData);
    }

    /**
     * التحقق من وجود ملف
     */
    public function hasFile(): bool
    {
        return !empty($this->file_path) && Storage::exists($this->file_path);
    }

    /**
     * الحصول على حجم الملف بصيغة قابلة للقراءة
     */
    public function getFormattedFileSize(): string
    {
        if (!$this->file_size) {
            return 'غير محدد';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return round($size, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * التحقق من إمكانية إعادة الإرسال
     */
    public function canRetry(): bool
    {
        $maxRetries = config('app.max_retry_attempts', 5);
        return $this->status === 'failed' && $this->retry_count < $maxRetries;
    }

    /**
     * زيادة عداد إعادة المحاولة
     */
    public function incrementRetryCount(): void
    {
        $this->update([
            'retry_count' => $this->retry_count + 1,
            'last_retry_at' => now()
        ]);
    }

    /**
     * نطاقات البحث
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeCanRetry($query)
    {
        $maxRetries = config('app.max_retry_attempts', 5);
        return $query->where('status', 'failed')
            ->where('retry_count', '<', $maxRetries);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month);
    }


}
