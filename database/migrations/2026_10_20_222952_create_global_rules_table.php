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
        Schema::create('global_rules', function (Blueprint $table) {
            $table->id();
            $table->integer('max_simultaneous_loans')->default(3);
            $table->integer('max_loan_days')->default(14);
            $table->integer('max_renewals')->default(2);
            $table->unsignedInteger('grace_hours')->default(1);
            $table->unsignedInteger('max_consultation_hours')->default(4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_rules');
    }
};
