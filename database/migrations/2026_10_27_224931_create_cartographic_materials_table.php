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
        Schema::create('cartographic_materials', function (Blueprint $table) {
            $table->foreignId('material_id')->primary()->constrained('materials')->restrictOnDelete();
            $table->string('scale')->nullable();
            $table->string('projection')->nullable();
            $table->string('coordinate')->nullable();
            $table->string('orientation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cartographic_materials');
    }
};
