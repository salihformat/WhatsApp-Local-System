<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('printer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('file_name');
            // مسار محلي مطلق للملف بعد تنزيله (المرفقات الواردة تصل كرابط بعيد ويجب تنزيلها أولاً)
            $table->string('file_path');
            $table->string('status')->default('pending'); // pending, printing, completed, failed
            $table->unsignedInteger('attempts')->default(0);
            $table->text('error_message')->nullable();
            $table->string('source')->default('whatsapp_incoming');
            $table->timestamp('printed_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_jobs');
    }
};
