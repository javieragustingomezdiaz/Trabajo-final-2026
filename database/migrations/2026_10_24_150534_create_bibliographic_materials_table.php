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
        Schema::create('bibliographic_materials', function (Blueprint $table) {
            $table->foreignId('material_id')->primary()->constrained('materials')->restrictOnDelete();
            $table->boolean('is_illustrated')->default(false);
            $table->string('binding_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bibliographic_materials');
    }
};
