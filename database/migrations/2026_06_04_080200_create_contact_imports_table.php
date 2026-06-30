<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('file_name')->comment('اسم الملف المرفوع');
            $table->string('file_path')->comment('مسار التخزين');
            $table->unsignedInteger('total_rows')->default(0)->comment('إجمالي الصفوف');
            $table->unsignedInteger('success_count')->default(0)->comment('عدد الصفوف الناجحة');
            $table->unsignedInteger('failed_count')->default(0)->comment('عدد الصفوف الفاشلة');
            $table->unsignedInteger('duplicate_count')->default(0)->comment('عدد المكررات');
            $table->unsignedInteger('updated_count')->default(0)->comment('عدد المحدثة');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->json('error_log')->nullable()->comment('سجل الأخطاء التفصيلي');
            $table->json('column_mapping')->nullable()->comment('ربط أعمدة الملف بالحقول');
            $table->foreignId('contact_group_id')->nullable()->constrained()->onDelete('set null')->comment('المجموعة التي سيتم إضافة العملاء إليها');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_imports');
    }
};
