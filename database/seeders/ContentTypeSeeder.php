<?php

namespace Database\Seeders;

use App\Models\ContentType;
use Illuminate\Database\Seeder;

class ContentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $terms = [
            'conjunto de datos cartográficos',
            'imagen cartográfica',
            'imagen cartográfica en movimiento',
            'imagen cartográfica táctil',
            'forma cartográfica táctil tridimensional',
            'forma cartográfica tridimensional',
            'conjunto de datos para computadora',
            'programa para computadora',
            'notación de movimientos',
            'música notada',
            'música interpretada',
            'sonidos',
            'palabra hablada',
            'imagen fija',
            'imagen táctil',
            'música notada táctil',
            'movimiento notado táctil',
            'texto táctil',
            'forma tridimensional táctil',
            'texto',
            'forma tridimensional',
            'imagen móvil tridimensional',
            'imagen móvil bidimensional',
            'performed movement',
        ];

        foreach ($terms as $term) {
            ContentType::updateOrCreate(['term' => $term]);
        }
    }
}