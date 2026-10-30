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
        Schema::create('loan_terms', function (Blueprint $table) {
            $table->id();
            $table->boolean('bookable')->default(true);
            $table->unsignedInteger('hours_on_standby')->nullable();
            $table->boolean('is_home_loanable')->default(true);
            $table->unsignedInteger('home_loan_days')->nullable();
            $table->boolean('allow_in_library_use')->default(false);
            $table->unsignedInteger('in_library_loan_hours')->nullable();
            $table->boolean('is_renewable')->default(false);
            $table->unsignedSmallInteger('max_renewal')->default(1);
            $table->unsignedInteger('renewal_days')->nullable();
            $table->unsignedSmallInteger('max_simultaneous_reservations')->nullable();
            $table->unsignedSmallInteger('max_simultaneous_loans')->nullable();
            $table->unsignedSmallInteger('sanctions_days_factor')->nullable();
            $table->unsignedInteger('damage_suspension_days')->nullable();
            $table->foreignId('material_type_id')->unique()->constrained('material_types')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_terms');
    }
};
