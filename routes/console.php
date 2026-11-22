<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::useCache('file');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reservations:process-queue')->everyMinute()->withoutOverlapping();
Schedule::command('circulations:mark-overdue')->everyMinute()->withoutOverlapping();
