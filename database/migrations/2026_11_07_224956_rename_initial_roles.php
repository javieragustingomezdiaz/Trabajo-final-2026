<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('roles')
            ->where('name', 'administrador')
            ->update(['name' => 'admin']);

        DB::table('roles')
            ->where('name', 'bibliotecario')
            ->update(['name' => 'librarian']);

        DB::table('roles')
            ->where('name', 'socio')
            ->update(['name' => 'member']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('roles')
            ->where('name', 'admin')
            ->update(['name' => 'administrador']);

        DB::table('roles')
            ->where('name', 'librarian')
            ->update(['name' => 'bibliotecario']);

        DB::table('roles')
            ->where('name', 'member')
            ->update(['name' => 'socio']);
    }
};
