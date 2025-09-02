<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            [
                'name' => '寿司',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '焼肉',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '居酒屋',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'イタリアン',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ラーメン',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('genres')->insert($genres);
    }
}
