<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationStatusSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'En espera',
            'Notificada',
            'Pendiente de retiro',
            'Retirada',
            'Cancelada',
            'Vencida',
            'Rechazada',
        ] as $name) {
            DB::table('reservation_statuses')->updateOrInsert(
                ['name' => $name],
                ['updated_at' => now(), 'created_at' => now()],
            );
        }
    }
}
