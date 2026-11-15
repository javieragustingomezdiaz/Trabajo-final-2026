<?php

namespace Database\Seeders;

use App\Models\MediaType;
use Illuminate\Database\Seeder;

class MediaTypeSeeder extends Seeder
{
    public function run(): void
    {
        $terms = [
            'audio',
            'microformas',
            'computadora',
            'microscópicos',
            'proyectados',
            'estereográficos',
            'sin mediación',
            'video',
        ];

        foreach ($terms as $term) {
            MediaType::updateOrCreate(['term' => $term]);
        }
    }
}