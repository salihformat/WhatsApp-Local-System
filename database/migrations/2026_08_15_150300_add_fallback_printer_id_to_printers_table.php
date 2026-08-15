<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * تحويل تلقائي عند التعطل: إن كانت هذه الطابعة غير سليمة حسب آخر فحص دوري (last_status_healthy،
 * راجع monitor:printers)، تُحوَّل مهام الطباعة الجديدة تلقائياً لهذه الطابعة الاحتياطية بدل التعليق
 * بانتظار تدخل يدوي — راجع PrintJobDispatcher::resolveHealthyPrinter().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->foreignId('fallback_printer_id')->nullable()->after('is_default')
                ->constrained('printers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fallback_printer_id');
        });
    }
};
