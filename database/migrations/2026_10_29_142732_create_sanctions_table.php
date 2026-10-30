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
        Schema::create('sanctions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start_on');
            $table->dateTime('end_on')->nullable();
            $table->dateTime('lifted_on')->nullable();
            $table->string('reason');
            $table->string('status', 40);
            $table->text('observation')->nullable();
            $table->foreignId('circulation_id')->nullable()->constrained('circulations')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sanctions');
    }
};
