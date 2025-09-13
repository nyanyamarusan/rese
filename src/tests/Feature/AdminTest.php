<?php

namespace Tests\Feature;

use App\Mail\NoticeMail;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_owner(): void
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin?tab=store');
        $response->assertStatus(200);

        $response = $this->post('/admin/store', [
            'name' => 'owner',
            'email' => 'owner@example.com',
            'password' => 'password',
        ]);

        $owner = Owner::where('email', 'owner@example.com')->first();
        $this->assertTrue(Hash::check('password', $owner->password));

        $response->assertStatus(302);
        $response->assertSessionHas('message', '店舗代表者を作成しました');
    }

    public function test_send(): void
    {
        Mail::fake();

        $admin = Admin::factory()->create();
        $users = User::factory()->count(3)->create();

        $this->actingAs($admin, 'admin');

        $response = $this->get('/admin?tab=send');
        $response->assertStatus(200);

        $response = $this->post('/admin/send', [
            'subject' => 'テスト件名',
            'body' => 'テスト本文',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('message', 'メールを送信しました');

        foreach ($users as $user) {
            Mail::assertSent(NoticeMail::class, function ($mail) use ($user) {
                return $mail->subjectText === 'テスト件名'
                    && $mail->bodyText === 'テスト本文'
                    && $mail->hasTo($user->email);
            });
        }
    }
}
