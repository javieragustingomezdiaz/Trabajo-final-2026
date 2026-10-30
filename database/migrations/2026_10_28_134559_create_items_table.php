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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('barcode')->unique(); // codigo_barra
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->foreignId('container_id')->constrained('containers')->restrictOnDelete();
            $table->foreignId('item_condition_id')->constrained('item_conditions');
            $table->foreignId('item_status_id')->constrained('item_statuses');
            $table->text('observation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
