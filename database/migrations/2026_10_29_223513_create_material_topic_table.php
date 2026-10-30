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
        Schema::create('material_topic', function (Blueprint $table) {
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->foreignId('topic_id')->constrained('topics')->restrictOnDelete();
            $table->unique(['material_id', 'topic_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_topic');
    }
};
