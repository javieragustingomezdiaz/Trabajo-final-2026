<?php

namespace Database\Seeders;

use App\Models\AuthorType;
use Illuminate\Database\Seeder;

class AuthorTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'Persona',
            'Entidad corporativa',
            'Familia',
            'Congreso o reunión',
        ] as $typeName) {
            AuthorType::firstOrCreate([
                'type_name' => $typeName,
            ]);
        }
    }
}
