<?php

namespace App\Console\Commands;

use App\Services\ReservationQueueService;
use Illuminate\Console\Command;

class ProcessReservationQueue extends Command
{
    protected $signature = 'reservations:process-queue';

    protected $description = 'Vence ofertas de reserva y notifica al siguiente socio de la cola';

    public function handle(ReservationQueueService $service): int
    {
        $sent = $service->process();
        $this->info("Avisos enviados: {$sent}");

        return self::SUCCESS;
    }
}
