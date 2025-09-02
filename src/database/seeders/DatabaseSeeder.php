<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\Reservation;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Owner::factory()->count(19)->create();
        $this->call([
            UserSeeder::class,
            AreaSeeder::class,
            GenreSeeder::class,
            ShopSeeder::class,
            LikeSeeder::class,
        ]);
        Reservation::factory()->count(2)->create();
        Reservation::factory()->visited()->count(4)->create();
    }
}
