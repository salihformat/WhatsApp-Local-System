<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * إضافة عمود is_active لجدول المستخدمين لدعم تفعيل/تعطيل حسابات المستخدمين.
 * المستخدمون المُعطَّلون لا يستطيعون تسجيل الدخول ولا يستلمون محادثات جديدة.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // القيمة الافتراضية true لضمان أن المستخدمين الحاليين يبقون مفعّلين بعد التحديث
            $table->boolean('is_active')->default(true)->after('is_available_for_assignment');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
