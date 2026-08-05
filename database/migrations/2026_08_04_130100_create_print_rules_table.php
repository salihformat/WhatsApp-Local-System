<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('print_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // ترتيب التقييم: الرقم الأصغر يُفحص أولاً، وأول قاعدة مطابقة نشطة تفوز
            $table->unsignedInteger('priority')->default(100);
            // phone_number: تطابق تام | phone_prefix: يبدأ بـ | keyword: كلمة داخل نص الرسالة | file_type: امتداد الملف
            $table->string('match_type');
            $table->string('match_value');
            $table->foreignId('printer_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('print_rules');
    }
};
