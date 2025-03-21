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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // Foreign key reference to users table
            $table->string('candidate_type')->nullable();
            $table->unsignedBigInteger('l_company_id')->nullable();
            $table->decimal('l_rate', 10, 2)->nullable();
            $table->boolean('l_aggrement')->default(false);
            $table->unsignedBigInteger('c_id')->nullable();
            $table->string('c_name')->nullable();
            $table->unsignedBigInteger('visa_status_id')->nullable();
            $table->date('visa_start_date')->nullable();
            $table->date('visa_end_date')->nullable();
            $table->date('id_start_date')->nullable();
            $table->date('id_end_date')->nullable();
            $table->string('city_state')->nullable();
            $table->string('project')->nullable();
            $table->decimal('c_rate', 10, 2)->nullable();
            $table->text('candidate_note')->nullable();
            $table->text('c_rate_note')->nullable();
            $table->string('position')->nullable();
            $table->string('client')->nullable();
            $table->boolean('lapt_received')->default(false);
            $table->unsignedBigInteger('pv_company_id')->nullable();
            $table->unsignedBigInteger('b_company_id')->nullable();
            $table->decimal('b_rate', 10, 2)->nullable();
            $table->text('b_rate_note')->nullable();
            $table->unsignedBigInteger('our_company_id')->nullable();
            $table->boolean('b_aggrement')->default(false);
            $table->boolean('c_aggrement')->default(false);
            $table->string('marketer')->nullable();
            $table->string('recruiter')->nullable();
            $table->string('b_due_terms_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
