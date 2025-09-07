<?php

use App\Console\Commands\SendReservationReminders;
use Illuminate\Support\Facades\Artisan;

Artisan::command('reminder:send', function () {
    $this->call(SendReservationReminders::class);
})->dailyAt('08:00');
