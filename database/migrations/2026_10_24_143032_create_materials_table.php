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
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('edition')->nullable();
            $table->date('publication_date')->nullable();
            $table->string('identifier_value')->nullable();
            $table->string('dimension')->nullable();
            $table->string('extent')->nullable();
            $table->text('description')->nullable();
            $table->string('cover')->nullable();

            $table->foreignId('publisher_id')->constrained('publishers')->restrictOnDelete();
            $table->foreignId('identifier_type_id')->constrained('identifier_types');
            $table->foreignId('material_type_id')->constrained('material_types');
            $table->foreignId('language_id')->constrained('languages');

            $table->foreignId('media_type_id')->constrained('media_types');
            $table->foreignId('content_type_id')->constrained('content_types');
            $table->foreignId('carrier_type_id')->constrained('carrier_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
