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
        'source_file_path',
        'file_type',
        'status',
        'attempts',
        'error_message',
        'source',
        'printed_at',
        'reminder_sent_at',
        'pages',
    ];

    protected $casts = [
        'attempts' => 'integer',
        'printed_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'pages' => 'integer',
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
     * تسميات حالات مهمة الطباعة (مترجمة حسب لغة الطلب الحالية) — نقطة مرجعية واحدة تُستخدم في الواجهة،
     * تصدير CSV، وردود واتساب (بدل تكرار نفس القائمة في كل مكان يعرض الحالة، كما كان الحال سابقاً).
     * ردود واتساب تُبنى دائماً بلغة APP_LOCALE الافتراضية (لا تتأثر بلغة متصفح الموظف الحالي).
     */
    public static function statusLabels(): array
    {
        return [
            'pending' => __('local_agent.print_status_pending'),
            'awaiting_approval' => __('local_agent.print_status_awaiting_approval'),
            'printing' => __('local_agent.print_status_printing'),
            'completed' => __('local_agent.print_status_completed'),
            'failed' => __('local_agent.print_status_failed'),
            'rejected' => __('local_agent.print_status_rejected'),
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
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
