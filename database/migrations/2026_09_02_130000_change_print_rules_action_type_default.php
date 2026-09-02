<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// [Fix] الافتراضي القديم print_and_send كان يربك المستخدم — لأن الملف غالباً وصل عبر واتساب
// أساساً (رسالة واردة)، فـ"إرسال" افتراضي غير مفهوم لمن لا يقصد إرسال الملف فعلياً عبر واتساب.
// الافتراضي الأكثر أماناً هو الطباعة فقط بلا أي إرسال تلقائي.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('print_rules', function (Blueprint $table) {
            $table->string('action_type')->default('print_only')->change();
        });
    }

    public function down(): void
    {
        Schema::table('print_rules', function (Blueprint $table) {
            $table->string('action_type')->default('print_and_send')->change();
        });
    }
};
