<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemHealthLog extends Model
{
    protected $fillable = [
        'pending_messages',
        'processing_messages',
        'failed_messages',
        'sent_messages',
        'old_pending_count',
        'recent_failed_count',
        'queue_backlog_count',
        'central_connected',
        'central_response_time_ms',
        'central_error',
        'checked_at',
    ];

    protected $casts = [
        'central_connected' => 'boolean',
        'checked_at' => 'datetime',
    ];
}
