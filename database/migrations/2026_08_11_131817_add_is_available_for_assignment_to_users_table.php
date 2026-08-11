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
        Schema::table('users', function (Blueprint $table) {
            // هل هذا المستخدم مرشّح لاستلام محادثات جديدة عبر التوزيع التلقائي (راجع
            // ConversationDistributionService)؟ يسمح باستثناء موظف مؤقتاً (إجازة/انشغال) من دورة
            // التوزيع بلا حاجة لتعطيل حسابه بالكامل أو حذفه من قائمة "مستخدمين محددين" في الإعدادات.
            $table->boolean('is_available_for_assignment')->default(true)->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_available_for_assignment');
        });
    }
};
