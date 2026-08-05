<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('printer_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('printer_id')->constrained()->cascadeOnDelete();
            $table->string('status'); // healthy, offline, error, unknown
            $table->boolean('is_healthy');
            $table->text('detail')->nullable();
            // هل تغيّرت الحالة عن الفحص السابق؟ (لتمييز أحداث التنبيه الفعلية عن الفحوصات الروتينية)
            $table->boolean('status_changed')->default(false);
            $table->timestamps();

            $table->index(['printer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('printer_status_logs');
    }
};
