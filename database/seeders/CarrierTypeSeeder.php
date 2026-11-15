<?php

namespace Database\Seeders;

use App\Models\CarrierType;
use Illuminate\Database\Seeder;

class CarrierTypeSeeder extends Seeder
{
    public function run(): void
    {
        $terms = [
            'cartucho sonoro',
            'cilindro sonoro',
            'disco sonoro',
            'carrete de banda sonora',
            'rollo sonoro',
            'audiocasete',
            'carrete de cinta sonora',
            'cinturón sonoro',
            'carrete de alambre sonoro',

            'tarjeta de computadora',
            'cartucho de chip para computadora',
            'disco de computadora',
            'cartucho de discos de computadora',
            'cartucho de cinta de computadora',
            'casete de cinta de computadora',
            'carrete de cinta de computadora',
            'recurso en línea',

            'tarjeta de apertura',
            'microficha',
            'casete de microficha',
            'cartucho de microfilme',
            'casete de microfilme',
            'carrete de microfilme',
            'tira de microfilme',
            'rollo de microfilme',
            'microopaco',

            'portaobjeto de microscopio',

            'cartucho de película',
            'casete de película',
            'carrete de película',
            'filmina',
            'fotobanda',
            'cartucho de fotobanda',
            'transparencia de retroproyector',
            'diapositiva',
            'rollo de película',

            'tarjeta estereográfica',
            'disco estereográfico',

            'tarjeta',
            'rotafolio',
            'rollo',
            'hoja',
            'volumen',
            'objeto',

            'cartucho de video',
            'casete de video',
            'rollo de videocinta',
            'videodisco',
        ];

        foreach ($terms as $term) {
            CarrierType::updateOrCreate(['term' => $term]);
        }
    }
}