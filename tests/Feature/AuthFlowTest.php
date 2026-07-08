<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_dashboard_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_registration_creates_user_and_sends_verification_email(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Тест Пользователь',
            'email' => 'test@example.com',
            'password' => 'secret-password-1',
            'password_confirmation' => 'secret-password-1',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->email_verified_at);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_unverified_user_cannot_open_dashboard(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect('/email/verify');
    }

    public function test_email_verification_link_verifies_user(): void
    {
        $user = User::factory()->unverified()->create();

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $this->actingAs($user)->get($url)->assertRedirect('/dashboard?verified=1');

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_verified_user_sees_dashboard_in_russian(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Сводка');
    }

    public function test_user_can_login_and_logout(): void
    {
        $user = User::factory()->create(['password' => 'secret-password-1']);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'secret-password-1',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticated();

        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
    }
}
