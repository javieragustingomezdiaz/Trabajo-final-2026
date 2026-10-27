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
        Schema::create('sound_materials', function (Blueprint $table) {
            $table->foreignId('material_id')->primary()->constrained('materials')->restrictOnDelete();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->string('recording_type')->nullable();
            $table->string('audio_channel')->nullable();
            $table->string('playback_speed')->nullable();
            $table->unsignedInteger('number_tracks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sound_materials');
    }
};
