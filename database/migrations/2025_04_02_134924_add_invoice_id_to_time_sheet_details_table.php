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
        Schema::table('time_sheet_details', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->after('time_sheet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('time_sheet_details', function (Blueprint $table) {
            $table->dropColumn('invoice_id');
        });
    }
};
