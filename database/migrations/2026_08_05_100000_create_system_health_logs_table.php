<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_health_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('pending_messages')->default(0);
            $table->unsignedInteger('processing_messages')->default(0);
            $table->unsignedInteger('failed_messages')->default(0);
            $table->unsignedInteger('sent_messages')->default(0);
            $table->unsignedInteger('old_pending_count')->default(0);
            $table->unsignedInteger('recent_failed_count')->default(0);
            // عدد المهام المتراكمة في طابور Laravel نفسه (queue jobs غير المُعالَجة بعد) —
            // اكتشفنا فعلياً تراكماً وصل لـ825 مهمة بسبب توقف عامل الطابور لفترة طويلة
            $table->unsignedInteger('queue_backlog_count')->default(0);
            $table->boolean('central_connected')->default(false);
            $table->unsignedInteger('central_response_time_ms')->nullable();
            $table->string('central_error')->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();

            $table->index('checked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_health_logs');
    }
};
