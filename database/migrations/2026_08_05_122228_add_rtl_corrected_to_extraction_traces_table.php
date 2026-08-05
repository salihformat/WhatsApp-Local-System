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
        Schema::table('extraction_traces', function (Blueprint $table) {
            $table->boolean('rtl_corrected')->default(false)->after('excluded');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extraction_traces', function (Blueprint $table) {
            $table->dropColumn('rtl_corrected');
        });
    }
};
