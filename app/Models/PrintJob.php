<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrintJob extends Model
{
    protected $fillable = [
        'message_id',
        'printer_id',
        'file_name',
        'file_path',
        'status',
        'attempts',
        'error_message',
        'source',
        'printed_at',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'printed_at' => 'datetime',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function printer()
    {
        return $this->belongsTo(Printer::class);
    }

    public function canRetry(): bool
    {
        return $this->status === 'failed' && $this->attempts < 3;
    }

    /**
     * المدة بين وصول طلب الطباعة (created_at) واكتماله فعلياً (printed_at)، بصيغة مقروءة —
     * لمقارنة أداء الطباعة بمرور الوقت واكتشاف أي تباطؤ غير طبيعي (طابعة بطيئة/شبكة/ملفات كبيرة).
     */
    public function getDurationForHumansAttribute(): ?string
    {
        if (!$this->printed_at || !$this->created_at) {
            return null;
        }

        $seconds = $this->created_at->diffInSeconds($this->printed_at);

        if ($seconds < 60) {
            return "{$seconds} ثانية";
        }

        $minutes = intdiv($seconds, 60);
        $remainingSeconds = $seconds % 60;

        return "{$minutes} دقيقة" . ($remainingSeconds > 0 ? " و{$remainingSeconds} ثانية" : '');
    }
}
