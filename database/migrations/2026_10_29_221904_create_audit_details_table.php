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
        Schema::create('audit_details', function (Blueprint $table) {
            $table->id();
            $table->string('field_name');
            $table->longText('old_value')->nullable();
            $table->longText('new_value')->nullable();
            $table->foreignId('audit_id')->constrained('audits')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_details');
    }
};
