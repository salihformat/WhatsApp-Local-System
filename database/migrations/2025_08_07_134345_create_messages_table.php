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
        Schema::create("messages", function (Blueprint $table) {
            $table->id();
//            $table->bigInteger('company_id')->nullable();
            $table->string("file_name")->nullable();
            $table->string("file_type")->nullable();
            $table->string("phone_number");
            $table->text("message_text")->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->string('file_path')->nullable();
//            $table->enum('message_type', ['text', 'document', 'image','vendo','audio', 'caption'])->default('text');
            $table->enum('message_type', ['text', 'media'])->default('text');
//            $table->enum('status', ['processing','pending', 'sending','sent', 'delivered', 'read', 'failed', 'no_whatsapp'])->default('processing');
            $table->string("status")->nullable();
            $table->timestamp("sent_at")->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->integer("retry_count")->default(0);
            $table->timestamp('last_retry_at')->nullable();
            $table->string("central_message_id")->nullable(); // ID from central system
            $table->text("error_message")->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['phone_number', 'created_at']);
            $table->index('central_message_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("messages");
    }
};
