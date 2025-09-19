<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use App\Notifications\ReservationReminder;
use Illuminate\Console\Command;

class SendReservationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->format('Y-m-d');

        $reservations = Reservation::where('date', $today)
            ->where('reminded', false)
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->user->notify(new ReservationReminder($reservation));
            $reservation->update(['reminded' => true]);
        }

        $this->info('リマインダー送信完了');
    }
}
