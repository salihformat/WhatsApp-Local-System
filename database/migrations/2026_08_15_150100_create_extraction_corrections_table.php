<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * التعلّم من التصحيح اليدوي: كل مرة يوافق/يرفض المسؤول ملفاً محجوزاً للمراجعة اليدوية (لأن رقم الجوال
 * استُخرج من مصدر منخفض الثقة)، نسجّل هنا القرار. إن تكرر اعتماد نفس الرقم لنفس مصدر الاستخراج عدة
 * مرات بلا أي رفض، يعتبره MonitorFolderCommand "موثوقاً بالتعلّم" ويتخطى المراجعة اليدوية تلقائياً
 * في المرات القادمة — راجع MonitorFolderCommand::isLearnedTrusted().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extraction_corrections', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->index();
            $table->string('source')->index();
            $table->enum('decision', ['approved', 'rejected']);
            $table->unsignedBigInteger('message_id')->nullable();
            $table->string('source_filename')->nullable();
            $table->timestamps();

            $table->index(['phone_number', 'source', 'decision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extraction_corrections');
    }
};
