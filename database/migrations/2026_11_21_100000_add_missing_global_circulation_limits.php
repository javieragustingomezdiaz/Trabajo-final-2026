<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('global_rules', function (Blueprint $table) {
            $table->unsignedInteger('max_simultaneous_reservations')->default(5)->after('max_simultaneous_loans');
            $table->unsignedInteger('reservation_offer_hours')->default(6)->after('max_simultaneous_reservations');
            $table->unsignedInteger('max_sanction_days')->default(30)->after('grace_hours');
        });
    }

    public function down(): void
    {
        Schema::table('global_rules', function (Blueprint $table) {
            $table->dropColumn([
                'max_simultaneous_reservations',
                'reservation_offer_hours',
                'max_sanction_days',
            ]);
        });
    }
};
