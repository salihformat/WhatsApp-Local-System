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
            // 'auto': يُطبع أي ملف مطابق فوراً بلا تدخل بشري. 'approval': يُحجز الطلب بحالة
            // awaiting_approval ولا يُطبع إلا بعد موافقة صريحة (زر في لوحة التحكم أو رد واتساب
            // "وافق <رقم المهمة>" من رقم المسؤول)، مفيد لطابعات تستهلك مستلزمات مكلفة أو مقيّدة.
            $table->string('print_mode')->default('auto')->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('printers', function (Blueprint $table) {
            $table->dropColumn('print_mode');
        });
    }
};
