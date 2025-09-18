<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Genre;
use App\Models\Owner;
use App\Models\Reservation;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
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
            'name' => 'other shop',
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

    public function test_select_shop(): void
    {
        $owner = Owner::factory()->create();
        $shop = Shop::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $selectedShop = Shop::factory()->create([
            'owner_id' => $owner->id,
        ]);

        $this->actingAs($owner, 'owner');

        $response = $this->get('/owner/show/' . $selectedShop->id);
        $response->assertStatus(200);
        $response->assertSee($selectedShop->name);
        $response->assertDontSee($shop->name);
    }

    public function test_reservation_list(): void
    {
        Carbon::setTestNow(Carbon::create('2025-09-02'));

        $owner = Owner::factory()->create();
        $shop = Shop::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $reservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
            'date' => '2025-09-10',
        ]);
        $visitedReservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
            'date' => '2025-09-01',
            'visited' => true,
        ]);

        $this->actingAs($owner, 'owner');

        $response = $this->get('/owner/show/' . $shop->id . '?tab=reservation');
        $response->assertStatus(200);

        $response->assertSee($reservation->shop->name);
        $response->assertSee($reservation->date->format('Y-m-d'));

        $response->assertDontSee($visitedReservation->date->format('Y-m-d'));
    }

    public function test_cannot_access_other_owner_shop(): void
    {
        $owner = Owner::factory()->create();
        $shop = Shop::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $otherOwner = Owner::factory()->create();
        $otherShop = Shop::factory()->create([
            'owner_id' => $otherOwner->id,
        ]);

        $this->actingAs($owner, 'owner');

        $response = $this->get('/owner/show/' . $otherShop->id);
        $response->assertStatus(404);
    }

    public function test_update_shop(): void
    {
        $owner = Owner::factory()->create();
        $oldImage = UploadedFile::fake()->create('old-image.jpg');
        Storage::disk('public')->putFileAs('shop-img', $oldImage, $oldImage->hashName());

        $shop = Shop::factory()->create([
            'owner_id' => $owner->id,
            'image' => $oldImage->hashName(),
        ]);

        $this->actingAs($owner, 'owner');

        $response = $this->get('/owner/show/' . $shop->id . '?tab=edit');
        $response->assertStatus(200);

        $response->assertSee($shop->name);
        $response->assertSee($shop->area->name);
        $response->assertSee($shop->genre->name);
        $response->assertSee($shop->open_time->format('H:i'));
        $response->assertSee($shop->close_time->format('H:i'));
        $response->assertSee($shop->detail);

        $image = UploadedFile::fake()->create('image.jpg');
        $area = Area::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->patch('/owner/update/' . $shop->id, [
            'name' => 'shop',
            'owner_id' => $owner->id,
            'area_id' => $area->id,
            'genre_id' => $genre->id,
            'open_time' => '10:00',
            'close_time' => '22:00',
            'image' => $image,
            'detail' => 'detail',
        ]);

        $this->assertDatabaseHas('shops', [
            'id' => $shop->id,
            'name' => 'shop',
            'owner_id' => $owner->id,
            'area_id' => $area->id,
            'genre_id' => $genre->id,
            'open_time' => '10:00',
            'close_time' => '22:00',
            'image' => $image->hashName(),
            'detail' => 'detail',
        ]);

        $this->assertDatabaseMissing('shops', [
            'image' => $oldImage->hashName(),
        ]);

        $shop = $shop->fresh();
        Storage::disk('public')->assertExists('shop-img/' . $shop->image);
        Storage::disk('public')->assertMissing('shop-img/' . $oldImage->hashName());

        $response->assertStatus(302);
        $response->assertSessionHas([
            'status' => 'success',
            'message' => '店舗情報を更新しました',
        ]);
    }

    public function test_checkin_success(): void
    {
        $owner = Owner::factory()->create();
        $shop = Shop::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $reservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
        ]);

        $this->actingAs($owner, 'owner');

        $response = $this->get('/owner/checkin/' . $reservation->checkin_token);
        $response->assertStatus(302);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'visited' => true,
        ]);

        $response->assertRedirect('/owner/show/' . $shop->id . '?tab=reservation');
        $response->assertSessionHas([
            'status' => 'success',
            'message' => '来店確認が完了しました',
        ]);
    }

    public function test_checkin_invalid_token(): void
    {
        $owner = Owner::factory()->create();

        $this->actingAs($owner, 'owner');

        $response = $this->get('/owner/checkin/' . 'invalid-token');
        $response->assertStatus(404);
    }

    public function test_checkin_already_visited(): void
    {
        $owner = Owner::factory()->create();
        $shop = Shop::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $reservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
            'visited' => true,
        ]);

        $this->actingAs($owner, 'owner');

        $response = $this->get('/owner/checkin/' . $reservation->checkin_token);
        $response->assertStatus(302);

        $response->assertRedirect('/owner/show/' . $shop->id . '?tab=reservation');
        $response->assertSessionHas([
            'status' => 'error',
            'message' => 'すでにチェックイン済みです',
        ]);
    }

    public function test_checkout(): void
    {
        $owner = Owner::factory()->create();
        $shop = Shop::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $reservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
        ]);
        $visitedReservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
            'date' => Carbon::today(),
            'visited' => true,
        ]);
        $checkoutReservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
            'visited' => true,
            'paid' => true,
        ]);

        $this->actingAs($owner, 'owner');

        $response = $this->get('/owner/show/' . $shop->id . '?tab=checkout');
        $response->assertStatus(200);

        $response->assertSee('checkout-box-' . $visitedReservation->id);

        $response->assertDontSee('checkout-box-' . $reservation->id);
        $response->assertDontSee('checkout-box-' . $checkoutReservation->id);
    }

    public function test_checkout_success(): void
    {
        $owner = Owner::factory()->create();
        $shop = Shop::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $reservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
            'visited' => true,
        ]);

        $this->actingAs($owner, 'owner');

        $mockSession = Mockery::mock('alias:Stripe\Checkout\Session');
        $mockSession->shouldReceive('create')->andReturn((object)[
            'id' => 'cs_test_123',
            'url' => 'https://checkout.stripe.com/pay/cs_test_123',
            'metadata' => ['reservation_id' => $reservation->id],
        ]);
        $mockSession->shouldReceive('retrieve')->with('cs_test_123')->andReturn((object)[
            'id' => 'cs_test_123',
            'payment_status' => 'paid',
            'metadata' => (object)['reservation_id' => $reservation->id],
        ]);

        $response = $this->post('/owner/checkout', [
            'reservation_id' => $reservation->id,
            'amount' => 1000,
        ]);

        $response->assertRedirect('https://checkout.stripe.com/pay/cs_test_123');
        $response = $this->get('/owner/checkout/success?session_id=cs_test_123');

        $response->assertRedirect('/owner/show/' . $shop->id . '?tab=checkout');
        $response->assertSessionHas([
            'status' => 'success',
            'message' => '支払いが完了しました',
        ]);

        $reservation->refresh();
        $this->assertTrue($reservation->paid);
    }

    public function test_checkout_failure(): void
    {
        $owner = Owner::factory()->create();
        $shop = Shop::factory()->create([
            'owner_id' => $owner->id,
        ]);
        $reservation = Reservation::factory()->create([
            'shop_id' => $shop->id,
            'visited' => true,
        ]);

        $this->actingAs($owner, 'owner');

        $mockSession = Mockery::mock('alias:Stripe\Checkout\Session');
        $mockSession->shouldReceive('retrieve')->with('cs_test_fail')->andReturn((object)[
            'id' => 'cs_test_fail',
            'payment_status' => 'unpaid',
            'metadata' => (object)['reservation_id' => $reservation->id],
        ]);

        $response = $this->get('/owner/checkout/success?session_id=cs_test_fail');

        $response->assertRedirect('/owner/show/' . $shop->id . '?tab=checkout');
        $response->assertSessionHas([
            'status' => 'error',
            'message' => '支払いが完了していません',
        ]);

        $reservation->refresh();
        $this->assertFalse($reservation->paid);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
