<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrinterStatusLog extends Model
{
    protected $fillable = [
        'printer_id',
        'status',
        'is_healthy',
        'detail',
        'status_changed',
    ];

    protected $casts = [
        'is_healthy' => 'boolean',
        'status_changed' => 'boolean',
    ];

    public function printer()
    {
        return $this->belongsTo(Printer::class);
    }
}
