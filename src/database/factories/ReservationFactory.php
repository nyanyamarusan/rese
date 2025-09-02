<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'shop_id' => function () {
                return Shop::inRandomOrder()->first()->id;
            },
            'date' => $this->faker->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'time' => function (array $attributes) {
                $shop = Shop::find($attributes['shop_id']);
                $time = fake()
                    ->dateTimeBetween("today {$shop->open_time}", "today {$shop->close_time}");

                $time->setTime($time->format('H'), 0);
                return $time->format('H:i');
            },
            'number' => $this->faker->numberBetween(1, 10),
            'checkin_token' => Str::uuid(),
            'visited' => false,
        ];
    }

    public function visited(): static
    {
        return $this->state(fn (array $attributes) => [
            'visited' => true,
        ]);
    }

    public function notVisited(): static
    {
        return $this->state(fn (array $attributes) => [
            'visited' => false,
        ]);
    }
}
