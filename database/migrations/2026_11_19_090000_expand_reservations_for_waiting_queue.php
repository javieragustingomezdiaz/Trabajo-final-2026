<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table): void {
            $table->dateTime('withdrawal_deadline')->nullable()->change();
            $table->foreignId('item_id')->nullable()->after('material_id')
                ->constrained('items')->nullOnDelete();
            $table->dateTime('notified_at')->nullable()->after('item_id');
            $table->dateTime('confirmation_expires_at')->nullable()->after('notified_at');
            $table->uuid('confirmation_token')->nullable()->unique()->after('confirmation_expires_at');
        });
    }

    public function down(): void
    {
        DB::table('reservations')
            ->whereNull('withdrawal_deadline')
            ->update(['withdrawal_deadline' => DB::raw('registration_date')]);

        Schema::table('reservations', function (Blueprint $table): void {
            $table->dropForeign(['item_id']);
            $table->dropUnique(['confirmation_token']);
            $table->dropColumn([
                'item_id',
                'notified_at',
                'confirmation_expires_at',
                'confirmation_token',
            ]);
            $table->dateTime('withdrawal_deadline')->nullable(false)->change();
        });
    }
};
