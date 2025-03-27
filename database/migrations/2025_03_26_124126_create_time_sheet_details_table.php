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
        Schema::create('time_sheet_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('time_sheet_id');
            $table->date('date_of_day');
            $table->string('day_name');
            $table->decimal('hours', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheet_details');
    }
};
