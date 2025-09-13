<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Shop;
use App\Models\User;
use App\Notifications\ReservationReminder;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_reminder(): void
    {
        Carbon::setTestNow('2025-09-13 08:00:00');

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);
        $shop = Shop::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'date' => '2025-09-13',
            'time' => '12:00',
        ]);

        Notification::fake();

        $this->artisan('reminder:send')->assertExitCode(0);

        $reservation->refresh();
        $this->assertTrue($reservation->reminded);

        Notification::assertSentTo(
            [$reservation->user],
            ReservationReminder::class
        );
    }
}
