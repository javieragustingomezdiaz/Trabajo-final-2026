<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ([
            'Disponible',
            'Prestado',
            'Reservado',
            'Reservado para retiro',
            'En reparación',
            'Perdido',
            'Retirado',
        ] as $name) {
            DB::table('item_statuses')->updateOrInsert(
                ['name' => $name],
                ['updated_at' => now(), 'created_at' => now()],
            );
        }
    }
}
