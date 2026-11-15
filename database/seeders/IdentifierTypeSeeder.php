<?php

namespace Database\Seeders;

use App\Models\IdentifierType;
use Illuminate\Database\Seeder;

class IdentifierTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['ISBN', 'Sin identificador'] as $name) {
            IdentifierType::updateOrCreate(['name' => $name]);
        }
    }
}