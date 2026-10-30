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
        Schema::create('circulations', function (Blueprint $table) {
            $table->id();
            $table->string('loan_type');
            $table->dateTime('loan_date');
            $table->dateTime('estimated_return_date');
            $table->dateTime('actual_return_date')->nullable();
            $table->text('observation')->nullable();
            $table->unsignedInteger('number_renewals')->default(0);
            $table->foreignId('item_id')->constrained('items')->restrictOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('loan_status_id')->constrained('loan_statuses')->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('circulations');
    }
};
