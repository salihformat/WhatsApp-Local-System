<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('extraction_traces', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('extension', 10)->nullable();
            // من أين جاء الرقم النهائي: filename / label / file_number / unlabeled_fallback / corrupted_fallback / env_fallback / none
            $table->string('source')->nullable();
            $table->string('matched_label')->nullable();
            $table->string('file_number')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            // مطابقات تم تجاهلها (رقم + الكلمة التي طابقتها + كلمة الاستبعاد التي رفضتها)
            $table->json('excluded')->nullable();
            $table->string('final_phone')->nullable();
            $table->timestamps();

            $table->index('filename');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('extraction_traces');
    }
};
