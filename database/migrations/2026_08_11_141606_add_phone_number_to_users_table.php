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
            // رقم واتساب الموظف الشخصي (الصيغة الدولية 966...) — اختياري؛ إن كان موجوداً، يصله إشعار
            // واتساب فعلي (بالإضافة إلى إشعار جرس النظام) عند تعيين محادثة له. راجع WhatsAppChannel
            // و App\Notifications\ConversationAssigned.
            $table->string('phone_number')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });
    }
};
