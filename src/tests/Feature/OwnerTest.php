<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Genre;
use App\Models\Owner;
use App\Models\Shop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_select_shop_list(): void
    {
        $owner = Owner::factory()->create();
        $otherOwner = Owner::factory()->create();
        $shops = Shop::factory()->count(3)->create([
            'owner_id' => $owner->id,
        ]);
        $otherShop = Shop::factory()->create([
            'owner_id' => $otherOwner->id,
        ]);

        $this->actingAs($owner, 'owner');

        $response = $this->get('/owner');
        $response->assertStatus(200);

        foreach ($shops as $shop) {
            $response->assertSee($shop->name);
        }

        $response->assertDontSee($otherShop->name);
    }

    public function test_create_shop(): void
    {
        $owner = Owner::factory()->create();
        $area = Area::factory()->create();
        $genre = Genre::factory()->create();
        $image = UploadedFile::fake()->create('image.jpg');

        $this->actingAs($owner, 'owner');

        $response = $this->post('/owner/store', [
            'name' => 'shop',
            'owner_id' => $owner->id,
            'area_id' => $area->id,
            'genre_id' => $genre->id,
            'open_time' => '09:00',
            'close_time' => '21:00',
            'image' => $image,
            'detail' => 'detail',
        ]);

        $this->assertDatabaseHas('shops', [
            'name' => 'shop',
            'owner_id' => $owner->id,
            'area_id' => $area->id,
            'genre_id' => $genre->id,
            'open_time' => '09:00',
            'close_time' => '21:00',
            'image' => $image->hashName(),
            'detail' => 'detail',
        ]);

        Storage::disk('public')->assertExists('shop-img/' . $image->hashName());

        $response->assertStatus(302);
        $response->assertSessionHas('message', '店舗を登録しました');
    }
}
