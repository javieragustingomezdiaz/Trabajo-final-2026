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
        Schema::table('patron_applicants', function (Blueprint $table) {
            $table->dropUnique(['dni']);
            $table->dropUnique(['email']);

            $table->dropColumn([
                'dni',
                'name',
                'last_name',
                'birth_date',
                'email',
                'phone',
                'address',
                'avatar',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patron_applicants', function (Blueprint $table) {
            $table->string('dni')->unique();
            $table->string('name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->string('email')->unique();
            $table->string('phone');
            $table->string('address');
            $table->string('avatar')->nullable();
        });
    }
};
