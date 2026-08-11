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
        // آخر مرة أُرسل فيها تذكير واتساب للمسؤول لهذا الطلب (طباعة أو إرسال) وهو لا يزال بانتظار
        // الموافقة — يمنع إرسال تذكير متكرر كل مرة يشتغل فيها أمر التذكير المجدول (كل 10 دقائق).
        Schema::table('print_jobs', function (Blueprint $table) {
            $table->timestamp('reminder_sent_at')->nullable()->after('source_file_path');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('reminder_sent_at')->nullable()->after('error_message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('print_jobs', function (Blueprint $table) {
            $table->dropColumn('reminder_sent_at');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('reminder_sent_at');
        });
    }
};
