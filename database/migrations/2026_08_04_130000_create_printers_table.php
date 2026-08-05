<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // الاسم كما هو مسجّل بالضبط في Windows (Get-Printer)، يُستخدم كوسيط -print-to
            $table->string('windows_printer_name');
            // 'document' مدعومة حالياً عبر SumatraPDF، 'thermal' محجوزة للتوسعة المستقبلية (ESC/POS)
            $table->string('type')->default('document');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printers');
    }
};
