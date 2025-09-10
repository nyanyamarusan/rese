<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\Reservation;
use Carbon\Carbon;
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
        Reservation::factory()->count(2)->create([
            'user_id' => 1,
        ]);
        Reservation::factory()->visited()->count(4)->create([
            'user_id' => 1,
        ]);
        Reservation::factory()->count(20)->create([
            'user_id' => 1,
            'shop_id' => 1,
        ]);
        Reservation::factory()->visited()->count(20)->create([
            'user_id' => 1,
            'shop_id' => 1,
            'date' => Carbon::today(),
        ]);
    }
}
