<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\Shop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_when_trying_to_like(): void
    {
        $shop = Shop::factory()->create();

        $response = $this->patch('/like/' . $shop->id);
        $response->assertRedirect('/login');
    }

    public function test_like():void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $this->actingAs($user);

        $response = $this->patch('/like/' . $shop->id);
        $response->assertStatus(302);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ]);

        $response = $this->post('/like/' . $shop->id);
        $response->assertStatus(302);

        $this->assertDatabaseMissing('likes', [
            'user_id' => $user->id,
            'shop_id' => $shop->id,
        ]);
    }

    public function test_reservation_validation_date_required(): void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/done', [
            'shop_id' => $shop->id,
            'date' => '',
            'time' => '12:00',
            'number' => 2,
        ]);

        $response->assertSessionHasErrors([
            'date' => '日付を選択してください',
        ]);
    }

    public function test_reservation_validation_date_after_or_equal(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/done', [
            'shop_id' => $shop->id,
            'date' => $fixedDate->subDay()->toDateString(),
            'time' => '12:00',
            'number' => 2,
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_reservation_validation_time_required(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/done', [
            'shop_id' => $shop->id,
            'date' => $fixedDate->toDateString(),
            'time' => '',
            'number' => 2,
        ]);

        $response->assertSessionHasErrors([
            'time' => '時間を選択してください',
        ]);
    }

    public function test_reservation_validation_time_unique(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        Reservation::factory()->create([
            'shop_id' => $shop->id,
            'date' => $fixedDate->toDateString(),
            'time' => '12:00',
            'number' => 2,
        ]);

        $this->actingAs($user);

        $response = $this->post('/done', [
            'shop_id' => $shop->id,
            'date' => $fixedDate->toDateString(),
            'time' => '12:00',
            'number' => 2,
        ]);

        $response->assertSessionHasErrors([
            'time' => '選択された時間はすでに予約されています',
        ]);
    }

    public function test_reservation_validation_number_required(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/done', [
            'shop_id' => $shop->id,
            'date' => $fixedDate->toDateString(),
            'time' => '12:00',
            'number' => '',
        ]);

        $response->assertSessionHasErrors([
            'number' => '人数を選択してください',
        ]);
    }

    public function test_reservation_number(): void
    {
        $shop = Shop::factory()->create();
        $response = $this->get('/detail/' . $shop->id);
        $response->assertStatus(200);

        for ($i = 1; $i <= 10; $i++) {
            $response->assertSee("{$i}人");
        }
    }

    public function test_reservation_guest_is_redirected(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $shop = Shop::factory()->create();
        $response = $this->post('/done', [
            'shop_id' => $shop->id,
            'date' => $fixedDate->toDateString(),
            'time' => '12:00',
            'number' => 2,
        ]);
        $response->assertRedirect('/login');
    }

    public function test_reservation_success(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/done', [
            'shop_id' => $shop->id,
            'date' => $fixedDate->addDay()->toDateString(),
            'time' => '12:00',
            'number' => 2,
        ]);

        $this->assertDatabaseHas('reservations', [
            'shop_id' => $shop->id,
            'date' => $fixedDate->addDay()->toDateString(),
            'time' => '12:00',
            'number' => 2,
        ]);

        $response->assertSee('予約が完了しました');
        $response->assertSee('戻る');
        $response = $this->get('/detail/' . $shop->id);
        $response->assertStatus(200);
    }

    public function test_mypage_guest_is_redirected(): void
    {
        $response = $this->get('/mypage');
        $response->assertRedirect('/login');
    }

    public function test_mypage_username(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/mypage');
        $response->assertStatus(200);

        $response->assertSee($user->name);
    }

    public function test_mypage_user_likes(): void
    {
        $user = User::factory()->create();
        $likedShops = Shop::factory()->count(3)->create();
        $unlikedShops = Shop::factory()->count(3)->create();
        $user->likes()->attach($likedShops);

        $this->actingAs($user);

        $response = $this->get('/mypage');
        $response->assertStatus(200);

        foreach ($likedShops as $likedShop) {
            $response->assertSee($likedShop->name);
        }
        foreach ($unlikedShops as $unlikedShop) {
            $response->assertDontSee($unlikedShop->name);
        }
    }

    public function test_mypage_user_reservations(): void
    {
        $user = User::factory()->create();
        $beforeShop = Shop::factory()->create();
        $afterShop = Shop::factory()->create();
        $beforeReservations = Reservation::factory()->count(3)->create([
            'user_id' => $user->id,
            'shop_id' => $beforeShop->id,
            'visited' => false,
        ]);
        $afterReservations = Reservation::factory()->count(3)->create([
            'user_id' => $user->id,
            'shop_id' => $afterShop->id,
            'visited' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get('/mypage');
        $response->assertStatus(200);

        $html = $response->getContent();

        foreach ($beforeReservations as $beforeReservation) {
            $this->assertMatchesRegularExpression(
                '予約状況' . $beforeReservation->shop->name . '/s',
                $html
            );
        }

        foreach ($afterReservations as $afterReservation) {
            $this->assertDoesNotMatchRegularExpression(
                '予約状況' . $afterReservation->shop->name . '/s',
                $html
            );
        }
    }

    public function test_mypage_user_visited_shops(): void
    {
        $user = User::factory()->create();
        $beforeShop = Shop::factory()->create();
        $afterShop = Shop::factory()->create();
        $beforeReservations = Reservation::factory()->count(3)->create([
            'user_id' => $user->id,
            'shop_id' => $beforeShop->id,
            'visited' => false,
        ]);
        $afterReservations = Reservation::factory()->count(3)->create([
            'user_id' => $user->id,
            'shop_id' => $afterShop->id,
            'visited' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get('/mypage');
        $response->assertStatus(200);

        $html = $response->getContent();

        foreach ($afterReservations as $afterReservation) {
            $this->assertMatchesRegularExpression(
                '来店履歴' . $afterReservation->shop->name . '/s',
                $html
            );
        }

        foreach ($beforeReservations as $beforeReservation) {
            $this->assertDoesNotMatchRegularExpression(
                '来店履歴*' . $beforeReservation->shop->name . '/s',
                $html
            );
        }
    }

    public function test_chancel_reservation(): void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'visited' => false,
        ]);

        $this->actingAs($user);

        $response = $this->post('/mypage/delete/' . $reservation->id);

        $this->assertDatabaseMissing('reservations', [
            'id' => $reservation->id,
        ]);

        $response->assertRedirect('/mypage/delete/' . $reservation->id);
        $response = $this->get('/mypage/delete/' . $reservation->id);
        $response->assertSee('予約がキャンセルされました');
        $response->assertSee('戻る');
        $response = $this->get('/mypage');
        $response->assertStatus(200);
        $response->assertDontSee($reservation->shop->name);
    }

    public function test_change_reservation_validation_date_required(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create();
        $shop = Shop::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'date' => '2025-09-12',
            'time' => '12:00',
            'number' => 2,
            'visited' => false,
        ]);

        $this->actingAs($user);

        $response = $this->patch('/mypage/update/' . $reservation->id, [
            'shop_id' => $shop->id,
            'date' => '',
            'time' => '12:00',
            'number' => 2,
        ]);

        $response->assertSessionHasErrors([
            'date' => '日付を選択してください',
        ]);
    }

    public function test_change_reservation_validation_date_after_or_equal(): void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create();

        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'date' => $fixedDate->toDateString(),
            'time' => '12:00',
            'number' => 2,
            'visited' => false,
        ]);

        $this->actingAs($user);

        $response = $this->patch('/mypage/update/' . $reservation->id, [
            'shop_id' => $shop->id,
            'date' => $fixedDate->subDay()->toDateString(),
            'time' => '12:00',
            'number' => 2,
        ]);

        $response->assertSessionHasErrors('date');
    }

    public function test_change_reservation_validation_time_required(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create();
        $shop = Shop::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'date' => '2025-09-12',
            'time' => '12:00',
            'number' => 2,
            'visited' => false,
        ]);

        $this->actingAs($user);

        $response = $this->patch('/mypage/update/' . $reservation->id, [
            'shop_id' => $shop->id,
            'date' => '2025-09-12',
            'time' => '',
            'number' => 2,
        ]);

        $response->assertSessionHasErrors([
            'time' => '時間を選択してください',
        ]);
    }

    public function test_change_reservation_validation_time_unique(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create();
        $shop = Shop::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'date' => '2025-09-12',
            'time' => '12:00',
            'number' => 2,
            'visited' => false,
        ]);

        Reservation::factory()->create([
            'shop_id' => $shop->id,
            'date' => '2025-09-12',
            'time' => '12:00',
            'number' => 2,
        ]);

        $this->actingAs($user);

        $response = $this->patch('/mypage/update/' . $reservation->id, [
            'shop_id' => $shop->id,
            'date' => '2025-09-12',
            'time' => '12:00',
            'number' => 2,
        ]);

        $response->assertSessionHasErrors([
            'time' => '選択された時間はすでに予約されています',
        ]);
    }

    public function test_change_reservation_validation_number_required(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create();
        $shop = Shop::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'date' => '2025-09-12',
            'time' => '12:00',
            'number' => 2,
            'visited' => false,
        ]);

        $this->actingAs($user);

        $response = $this->patch('/mypage/update/' . $reservation->id, [
            'shop_id' => $shop->id,
            'date' => '2025-09-12',
            'time' => '12:00',
            'number' => '',
        ]);

        $response->assertSessionHasErrors([
            'number' => '人数を選択してください',
        ]);
    }

    public function test_mypage_reservation_number(): void
    {
        $shop = Shop::factory()->create();
        $response = $this->get('/detail/' . $shop->id);
        $response->assertStatus(200);

        for ($i = 1; $i <= 10; $i++) {
            $response->assertSee("{$i}人");
        }
    }

    public function test_change_reservation_success(): void
    {
        $fixedDate = Carbon::create(2025, 7, 1);
        Carbon::setTestNow($fixedDate);

        $user = User::factory()->create();
        $shop = Shop::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'date' => '2025-09-12',
            'time' => '12:00',
            'number' => 2,
            'visited' => false,
        ]);

        $this->actingAs($user);

        $response = $this->patch('/mypage/update/' . $reservation->id, [
            'date' => '2025-09-15',
            'time' => '13:00',
            'number' => 1,
        ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'date' => '2025-09-15',
            'time' => '13:00',
            'number' => 1,
        ]);

        $response->assertSee('予約を変更しました');
        $response->assertSee('戻る');
        $response = $this->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee($reservation->shop->name);
        $response->assertSee('2025-09-15');
        $response->assertSee('13:00');
        $response->assertSee('1人');
    }

    public function test_review(): void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'visited' => true,
        ]);

        $this->actingAs($user);

        $response = $this->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee($reservation->shop->name);
        $response->assertSee('レビューする');

        $response = $this->get('/review/' . $reservation->id);
        $response->assertStatus(200);
        $response->assertSee($reservation->shop->name);

        $response = $this->post('/review/post/' . $reservation->id, [
            'rating' => 5,
            'comment' => 'とても良かったです',
        ]);

        $this->assertDatabaseHas('reviews', [
            'reservation_id' => $reservation->id,
            'rating' => 5,
            'comment' => 'とても良かったです',
        ]);

        $response->assertSee('レビューを送信しました');
        $response->assertSee('戻る');
        $response = $this->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee($reservation->shop->name);
        $response->assertSee('レビュー済み');
    }

    public function test_mypage_show_can_reservation_time(): void
    {
        $fixedTime = Carbon::create(2025, 7, 1, 12, 0, 0);
        Carbon::setTestNow($fixedTime);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $shop = Shop::factory()->create([
            'open_time' => '09:00',
            'close_time' => '21:00',
        ]);

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'date' => $fixedTime->toDateString(),
            'time' => '17:00',
        ]);

        Reservation::factory()->create([
            'user_id' => $otherUser->id,
            'shop_id' => $shop->id,
            'date' => $fixedTime->toDateString(),
            'time' => '15:00',
        ]);

        $this->actingAs($user);

        $response = $this->getJson("/mypage/update/{$reservation->id}/times?date=" . $fixedTime->toDateString());
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

    public function test_show_qr(): void
    {
        $user = User::factory()->create();
        $shop = Shop::factory()->create();
        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'shop_id' => $shop->id,
            'date' => '2025-09-12',
            'time' => '12:00',
            'number' => 2,
            'visited' => false,
        ]);

        $this->actingAs($user);

        $response = $this->get('/mypage');
        $response->assertSee('QRコード');

        $response = $this->get('/reservation/' . $reservation->id . '/qr');
        $response->assertStatus(200);
        $response->assertSee($reservation->shop->name);
        $response->assertSee($reservation->checkin_token);
    }
}
