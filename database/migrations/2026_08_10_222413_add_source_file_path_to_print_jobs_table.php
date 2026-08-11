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
            // المسار الفعلي للملف الأصلي داخل مجلد الطباعة المحلي (C:\PrintMonitor\print\<طابعة>\processing)
            // لمهام مصدرها 'print_folder' فقط — يُستخدم لاحقاً لنقل الملف إلى archive/failed عند اكتمال
            // المهمة أو فشلها أو رفضها. غير ذلك (whatsapp_incoming/monitor_folder) يبقى null، فلا يوجد
            // ملف مجلد طباعة مستقل يجب نقله (الملف الأصلي يخصّ مجلد المراقبة الرئيسي وليس هذا المجلد).
            $table->string('source_file_path')->nullable()->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            $table->dropColumn('source_file_path');
        });
    }
};
