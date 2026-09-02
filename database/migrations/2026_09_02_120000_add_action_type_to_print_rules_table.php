<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_rules', function (Blueprint $table) {
            // print_and_send (السلوك القديم/الافتراضي): طباعة + إرسال معاً | print_only: طباعة بلا إرسال
            // | send_only: إرسال بلا طباعة | save_only: حفظ فقط بلا طباعة ولا إرسال
            // | hold_for_approval: تعليق الإجراءين حتى موافقة يدوية
            $table->string('action_type')->default('print_and_send')->after('match_type');

            // لم يعد printer_id مطلوباً لكل قاعدة — إجراءات send_only/save_only/hold_for_approval
            // لا تحتاج طابعة إطلاقاً.
            $table->foreignId('printer_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('print_rules', function (Blueprint $table) {
            $table->dropColumn('action_type');
            $table->foreignId('printer_id')->nullable(false)->change();
        });
    }
};
