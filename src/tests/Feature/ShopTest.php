<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Genre;
use App\Models\Reservation;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_index(): void
    {
        $shops = Shop::factory()->count(10)->create();

        $response = $this->get('/');
        $response->assertStatus(200);

        foreach ($shops as $shop) {
            $response->assertSee($shop->name);
            $response->assertSee($shop->area->name);
            $response->assertSee($shop->genre->name);
            $response->assertSee($shop->image);
        }
    }

    public function test_search_area(): void
    {
        $tokyo = Area::factory()->create(['name' => '東京都']);
        $osaka = Area::factory()->create(['name' => '大阪府']);

        Shop::factory()->create(['area_id' => $tokyo->id]);
        Shop::factory()->create(['area_id' => $osaka->id]);

        $shops = Shop::query()->areaSearch($tokyo->id)->get();
        $this->assertCount(1, $shops);
        $this->assertEquals($tokyo->id, $shops->first()->area_id);

        $response = $this->get('/search?area_id=' . $tokyo->id);
        $response->assertStatus(200);
        $response->assertSee('#東京都');
        $response->assertDontSee('#大阪府');
    }

    public function test_search_genre(): void
    {
        $sushi = Genre::factory()->create(['name' => '寿司']);
        $yakiniku = Genre::factory()->create(['name' => '焼肉']);

        Shop::factory()->create(['genre_id' => $sushi->id]);
        Shop::factory()->create(['genre_id' => $yakiniku->id]);

        $shops = Shop::query()->genreSearch($sushi->id)->get();
        $this->assertCount(1, $shops);
        $this->assertEquals($sushi->id, $shops->first()->genre_id);

        $response = $this->get('/search?genre_id=' . $sushi->id);
        $response->assertStatus(200);
        $response->assertSee('#寿司');
        $response->assertDontSee('#焼肉');
    }

    public function test_search_keyword(): void
    {
        Shop::factory()->create(['name' => '寿司屋']);
        Shop::factory()->create(['name' => '焼肉屋']);

        $shops = Shop::query()->keywordSearch('寿司')->get();
        $this->assertCount(1, $shops);
        $this->assertEquals('寿司屋', $shops->first()->name);

        $response = $this->get('/search?keyword=寿司');
        $response->assertStatus(200);
        $response->assertSee('寿司屋');
        $response->assertDontSee('焼肉屋');
    }

    public function test_show(): void
    {
        $shop = Shop::factory()->create();

        $response = $this->get('/detail/' . $shop->id);
        $response->assertStatus(200);
        $response->assertSee($shop->name);
        $response->assertSee($shop->area->name);
        $response->assertSee($shop->genre->name);
        $response->assertSee($shop->image);
        $response->assertSee($shop->detail);
    }

    public function test_show_can_reservation_time(): void
    {
        $fixedTime = Carbon::create(2025, 7, 1, 12, 0, 0);
        Carbon::setTestNow($fixedTime);

        $shop = Shop::factory()->create([
            'open_time' => '09:00',
            'close_time' => '21:00',
        ]);

        Reservation::factory()->create([
            'shop_id' => $shop->id,
            'date' => $fixedTime->toDateString(),
            'time' => '15:00',
        ]);

        $response = $this->getJson("/detail/{$shop->id}/times?date=" . $fixedTime->toDateString());
        $response->assertStatus(200);

        $availableTimes = $response->json();

        $this->assertNotContains('15:00', $availableTimes);
        $this->assertNotContains('21:00', $availableTimes);
        $this->assertContains('13:00', $availableTimes);
        $this->assertContains('14:00', $availableTimes);
        $this->assertContains('16:00', $availableTimes);

        $nowHour = (int) $fixedTime->format('H');
        foreach ($availableTimes as $time) {
            $hour = (int) explode(':', $time)[0];
            $this->assertGreaterThanOrEqual($nowHour, $hour);
        }
    }
}
