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
        return [
            'name' => fake()->company(),
            'area_id' => Area::factory(),
            'genre_id' => Genre::factory(),
            'owner_id' => Owner::factory(),
            'open_time' => fake()->dateTimeBetween('08:00', '12:00')->format('H:i'),
            'close_time' => fake()->dateTimeBetween('19:00', '23:00')->format('H:i'),
            'image' => fake()->imageUrl(),
            'detail' => fake()->text(),
        ];
    }
}
