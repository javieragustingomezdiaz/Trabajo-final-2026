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
        Schema::table('users', function (Blueprint $table) {
            $table->string('dni')->unique()->after('id');
            $table->string('last_name')->after('name');
            $table->date('birth_date');
            $table->string('phone');
            $table->string('address');
            $table->string('avatar')->nullable();
            $table->foreignId('role_id')->constrained('roles')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn([
                'dni', 
                'last_name', 
                'birth_date', 
                'phone', 
                'address', 
                'avatar',
                'role_id'
            ]);
        });
    }
};
