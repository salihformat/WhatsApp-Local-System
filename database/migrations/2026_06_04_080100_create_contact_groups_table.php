<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_groups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('central_id')->nullable()->comment('معرف المجموعة في النظام المركزي');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->comment('اسم المجموعة');
            $table->text('description')->nullable();
            $table->string('color', 7)->nullable()->default('#128C7E')->comment('لون المجموعة بصيغة HEX');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('user_id');
            $table->unique(['user_id', 'name'], 'contact_groups_user_name_unique');
        });

        // جدول وسيط: ربط جهات الاتصال بالمجموعات (Many-to-Many)
        Schema::create('contact_group_members', function (Blueprint $table) {
            $table->foreignId('contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_group_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->primary(['contact_id', 'contact_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_group_members');
        Schema::dropIfExists('contact_groups');
    }
};
