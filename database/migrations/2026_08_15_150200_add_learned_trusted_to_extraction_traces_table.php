<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extraction_traces', function (Blueprint $table) {
            $table->boolean('learned_trusted')->default(false)->after('pdf_ocr_used');
        });
    }

    public function down(): void
    {
        Schema::table('extraction_traces', function (Blueprint $table) {
            $table->dropColumn('learned_trusted');
        });
    }
};
