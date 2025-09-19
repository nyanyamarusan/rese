<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Owner;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->app->singleton(VerifyEmailViewResponse::class, function () {
            return new class implements VerifyEmailViewResponse
            {
                public function toResponse($request)
                {
                    return response()->view('auth.verify-email');
                }
            };
        });
    }

    public function test_register_validate_required_name(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }

    public function test_register_validate_required_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_register_validate_email_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスの形式で入力してください',
        ]);
    }

    public function test_register_validate_unique_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'このメールアドレスは既に登録されています',
        ]);
    }

    public function test_register_validate_required_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_register_validate_min_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'pass',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    public function test_register_success(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);

        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'test',
            'email' => 'test@example.com',
        ]);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue(Hash::check('password', $user->password));

        $response->assertRedirect('/email/verify');
    }

    public function test_login_validate_required_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_login_validate_email_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスの形式で入力してください',
        ]);
    }

    public function test_login_validation_wrong(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function test_login_validate_required_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_login_validate_min_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'pass',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    public function test_login_success_with_admin(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated('admin');

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        $this->assertGuest('admin');

        $response->assertRedirect('/');
    }

    public function test_login_success_with_owner(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        Owner::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/owner/login', [
            'email' => 'owner@example.com',
            'password' => 'password',
        ]);
        $this->assertAuthenticated('owner');

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        $this->assertGuest('owner');

        $response->assertRedirect('/');
    }

    public function test_login_validate_required_email_admin(): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_login_validate_email_email_admin(): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'test',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスの形式で入力してください',
        ]);
    }

    public function test_login_validation_wrong_admin(): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'wrong@example.com',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function test_login_validate_required_password_admin(): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_login_validate_min_password_admin(): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'pass',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    public function test_login_ratelimiter_admin(): void
    {
        Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        RateLimiter::clear('login|admin@example.com|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', [
                'email' => 'admin@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン試行回数が多すぎます。しばらく待ってください。',
        ]);
    }

    public function test_login_success_admin_with_user(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated('admin');

        $this->assertGuest();

        $response->assertRedirect('/admin');
    }

    public function test_login_success_admin_with_owner(): void
    {
        $admin = Admin::factory()->create([
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $owner = Owner::factory()->create();

        $this->actingAs($owner, 'owner');
        $this->assertAuthenticated('owner');

        $response = $this->get('/admin/login');
        $response->assertStatus(200);

        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated('admin');

        $this->assertGuest('owner');

        $response->assertRedirect('/admin');
    }

    public function test_login_validate_required_email_owner(): void
    {
        Owner::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/owner/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_login_validate_email_email_owner(): void
    {
        Owner::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/owner/login', [
            'email' => 'owner',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスの形式で入力してください',
        ]);
    }

    public function test_login_validation_wrong_owner(): void
    {
        Owner::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/owner/login', [
            'email' => 'wrong@example.com',
            'password' => 'password',
        ]);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    public function test_login_validate_required_password_owner(): void
    {
        Owner::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/owner/login', [
            'email' => 'owner@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_login_validate_min_password_owner(): void
    {
        Owner::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/owner/login', [
            'email' => 'owner@example.com',
            'password' => 'pass',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }

    public function test_login_ratelimiter_owner(): void
    {
        Owner::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        RateLimiter::clear('login|owner@example.com|127.0.0.1');

        for ($i = 0; $i < 5; $i++) {
            $this->post('/owner/login', [
                'email' => 'owner@example.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->post('/owner/login', [
            'email' => 'owner@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'ログイン試行回数が多すぎます。しばらく待ってください。',
        ]);
    }

    public function test_login_success_owner_with_user(): void
    {
        $owner = Owner::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $user = User::factory()->create();

        $this->actingAs($user);
        $this->assertAuthenticated();

        $response = $this->get('/owner/login');
        $response->assertStatus(200);

        $response = $this->post('/owner/login', [
            'email' => 'owner@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated('owner');

        $this->assertGuest();

        $response->assertRedirect('/owner');
    }

    public function test_login_success_owner_with_admin(): void
    {
        $owner = Owner::factory()->create([
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
        ]);

        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin');
        $this->assertAuthenticated('admin');

        $response = $this->get('/owner/login');
        $response->assertStatus(200);

        $response = $this->post('/owner/login', [
            'email' => 'owner@example.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated('owner');

        $this->assertGuest('admin');

        $response->assertRedirect('/owner');
    }

    public function test_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_logout_admin(): void
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');

        $response = $this->post('/logout');
        $response->assertRedirect('/admin/login');
        $this->assertGuest('admin');
    }

    public function test_logout_owner(): void
    {
        $owner = Owner::factory()->create();
        $this->actingAs($owner, 'owner');

        $response = $this->post('/logout');
        $response->assertRedirect('/owner/login');
        $this->assertGuest('owner');
    }

    public function test_user_receives_verification_email()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_show_verification_screen_and_verify(): void
    {
        Event::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user);
        $response = $this->get('/email/verify');
        $response->assertStatus(200);
        $response->assertSeeText('認証する');

        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $verifyResponse = $this->actingAs($user)->get($verifyUrl);
        $verifyResponse->assertRedirect('/thanks');
        $thanksResponse = $this->get('/thanks');
        $thanksResponse->assertSee('会員登録ありがとうございます');
        $thanksResponse->assertSee('ログインする');
        $thanksResponse->assertSee('/login');

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    public function test_verification_email_can_be_resent()
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertRedirect();

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
