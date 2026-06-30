<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('central_id')->nullable()->comment('معرف جهة الاتصال في النظام المركزي');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('المستخدم المالك');
            $table->string('phone_number', 20)->comment('رقم الهاتف - يُخزن بصيغة دولية');
            $table->string('name', 255)->comment('الاسم الكامل للعميل');
            $table->string('file_number', 100)->nullable()->comment('رقم الملف الخاص بالعميل');
            $table->string('email')->nullable();
            $table->string('company_name')->nullable()->comment('اسم الشركة أو المؤسسة');
            $table->text('notes')->nullable()->comment('ملاحظات إضافية');
            $table->json('tags')->nullable()->comment('وسوم تصنيفية');
            $table->json('custom_fields')->nullable()->comment('حقول مخصصة إضافية');
            $table->boolean('is_favorite')->default(false)->comment('هل العميل مفضل');
            $table->timestamp('last_contacted_at')->nullable()->comment('آخر تواصل');
            $table->unsignedInteger('total_messages')->default(0)->comment('عدد الرسائل المرسلة');
            $table->enum('sync_status', ['local_only', 'synced', 'pending_sync', 'sync_failed'])->default('local_only')->comment('حالة المزامنة مع المركزي');
            $table->timestamp('synced_at')->nullable()->comment('تاريخ آخر مزامنة ناجحة');
            $table->timestamps();

            // فهارس لتحسين الأداء
            $table->index('user_id');
            $table->index('phone_number');
            $table->index('central_id');
            $table->index('sync_status');
            $table->index('file_number');
            $table->unique(['user_id', 'phone_number'], 'contacts_user_phone_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
