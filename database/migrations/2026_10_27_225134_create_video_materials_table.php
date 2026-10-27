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
        Schema::create('video_materials', function (Blueprint $table) {
            $table->foreignId('material_id')->primary()->constrained('materials')->restrictOnDelete();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('video_format')->nullable();
            $table->string('aspect_ratio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_materials');
    }
};
