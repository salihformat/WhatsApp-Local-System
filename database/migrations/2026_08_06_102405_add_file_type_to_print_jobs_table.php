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
        Schema::table('print_jobs', function (Blueprint $table) {
            // [Fix 2026-08-06] واتساب لا يرسل اسم ملف للصور إطلاقاً (بخلاف المستندات)، ورابط التخزين
            // المُرحَّل (S3) يستخدم مُعرِّفاً عشوائياً بلا امتداد — فيتعذّر تحديد نوع الملف عند تنزيله
            // محلياً للطباعة. mime_type (المتوفر دائماً في هذه الحالة) هو المصدر البديل الموثوق.
            $table->string('file_type')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            $table->dropColumn('file_type');
        });
    }
};
