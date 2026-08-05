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
        Schema::table('printers', function (Blueprint $table) {
            // هل تعريف/اتصال هذه الطابعة يُبلّغ فعلياً عن أعطالها الحقيقية (نفاد ورق/حبر/انحشار)
            // عبر Windows بشكل موثوق؟ ثبت عملياً (فحص حي مباشر بتاريخ 2026-08-05) أن بعض التعريفات
            // (خصوصاً USB/MFNP العامة مثل UFRII LT) لا تُبلّغ عن هذه الحالات إطلاقاً مهما كانت طريقة
            // الفحص (WMI أو قائمة انتظار الطباعة) — القيمة الافتراضية false (غير موثوقة) حتى يتأكد
            // المسؤول يدوياً من طابعته الخاصة ويُفعّلها من لوحة التحكم.
            $table->boolean('supports_status_check')->default(false)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->dropColumn('supports_status_check');
        });
    }
};
