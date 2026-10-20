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
        Schema::create('exception_days', function (Blueprint $table) {
            $table->id();
            $table->date('exception_date');
            $table->string('reason');
            $table->boolean('closed_all_day')->default(true);
            $table->time('opening_hours')->nullable();
            $table->time('closing_hours')->nullable();
            $table->boolean('happens_frequently')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exception_days');
    }
};
