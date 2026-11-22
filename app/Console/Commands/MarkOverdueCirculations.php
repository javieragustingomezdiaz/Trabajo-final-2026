<?php

namespace App\Console\Commands;

use App\Services\CirculationStatusService;
use Illuminate\Console\Command;

class MarkOverdueCirculations extends Command
{
    protected $signature = 'circulations:mark-overdue';

    protected $description = 'Marca como demorados los préstamos que superaron su fecha estimada de devolución';

    public function handle(CirculationStatusService $service): int
    {
        $updated = $service->markOverdue();
        $this->info("Préstamos marcados como demorados: {$updated}");

        return self::SUCCESS;
    }
}
