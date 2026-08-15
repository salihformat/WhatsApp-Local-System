<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * كشف التكرار: يُحفظ MD5 لمحتوى كل ملف يصل عبر مجلد المراقبة، للتحقق قبل الإرسال من أن نفس الملف
 * (بالمحتوى، وليس فقط بالاسم) لم يُرسَل مسبقاً لنفس الرقم خلال نافذة زمنية قريبة — راجع
 * MonitorFolderCommand::isDuplicateFile().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('file_hash', 32)->nullable()->after('file_path')->index();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('file_hash');
        });
    }
};
