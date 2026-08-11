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
            // عدد صفحات هذه المهمة تحديداً (يُحسب فعلياً من ملف PDF النهائي الجاهز للطباعة، أو 1
            // لكل صورة — راجع ProcessPrintJob). null إن لم يُحسب بعد (لم تكتمل المهمة) أو تعذّر حسابه.
            $table->unsignedInteger('pages')->nullable()->after('printed_at');
        });

        Schema::table('printers', function (Blueprint $table) {
            // إجمالي الصفحات المطبوعة فعلياً على هذه الطابعة منذ إضافتها — تقدير تقريبي لتخطيط
            // استهلاك الحبر/الورق، وليس عداداً رسمياً دقيقاً (لا يعكس صفحات فشلت الطباعة فعلياً رغم
            // اجتياز SumatraPDF بلا خطأ برمجي).
            $table->unsignedBigInteger('pages_printed')->default(0)->after('last_checked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            $table->dropColumn('pages');
        });

        Schema::table('printers', function (Blueprint $table) {
            $table->dropColumn('pages_printed');
        });
    }
};
