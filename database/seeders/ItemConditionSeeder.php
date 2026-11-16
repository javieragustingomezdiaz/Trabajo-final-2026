<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Nuevo', 'Bueno', 'Regular', 'Dañado'] as $name) {
            DB::table('item_conditions')->updateOrInsert(
                ['name' => $name],
                ['updated_at' => now(), 'created_at' => now()],
            );
        }
    }
}
