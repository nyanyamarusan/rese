<?php

namespace Database\Factories;

use App\Models\Area;
use App\Models\Genre;
use App\Models\Owner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $openHours = range(8, 12);
        $openHour = $openHours[array_rand($openHours)];
        $open_time = sprintf('%02d:00', $openHour);
        $closeHours = range(19, 23);
        $closeHour = $closeHours[array_rand($closeHours)];
        $close_time = sprintf('%02d:00', $closeHour);

        return [
            'name' => fake()->company(),
            'area_id' => Area::factory(),
            'genre_id' => Genre::factory(),
            'owner_id' => Owner::factory(),
            'open_time' => $open_time,
            'close_time' => $close_time,
            'image' => fake()->imageUrl(),
            'detail' => fake()->text(),
        ];
    }
}
