<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MaterialType;

class MaterialTypeSeeder extends Seeder
{
     public function run(): void
    {
        $types = [
            ['name' => 'Libro', 'group' => 'bibliographic'],
            ['name' => 'Publicación seriada', 'group' => 'serial_publication'],
            ['name' => 'Mapa', 'group' => 'cartographic'],
            ['name' => 'Material gráfico', 'group' => 'graph'],
            ['name' => 'Partitura', 'group' => 'musical'],
            ['name' => 'Grabación sonora', 'group' => 'sound'],
            ['name' => 'Video', 'group' => 'video'],
            ['name' => 'Objeto', 'group' => 'object'],
        ];

        foreach ($types as $type) {
            MaterialType::updateOrCreate(
                ['name' => $type['name']],
                ['group' => $type['group']]
            );
        }
    }
}
