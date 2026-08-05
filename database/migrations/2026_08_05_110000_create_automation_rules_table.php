<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('priority')->default(100);
            // phone_number, phone_prefix, keyword (نفس مفهوم PrintRule، بمطابقة جزئية بفواصل)
            $table->string('match_type');
            $table->string('match_value');
            // assign_user: تعيين المحادثة لموظف | internal_note: إضافة ملاحظة داخلية | auto_reply: رد تلقائي فوري
            $table->string('action_type');
            $table->text('action_value');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_rules');
    }
};
