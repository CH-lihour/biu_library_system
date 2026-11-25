<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_email_shows_error_message(): void
    {
        $this->from(route('login'))
            ->followingRedirects()
            ->post(route('store'), [
                'email'    => 'nobody@example.com',
                'password' => 'password',
            ])
            ->assertOk()
            ->assertSee('Invalid email or password')
            ->assertSee('value="nobody@example.com"', false);

        $this->assertGuest();
    }

    public function test_soft_deleted_user_cannot_login(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->from(route('login'))
            ->post(route('store'), [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertSessionHasErrors(['email' => 'Invalid email or password.']);

        $this->assertGuest();
    }

    public function test_short_password_shows_validation_message(): void
    {
        $this->from(route('login'))
            ->followingRedirects()
            ->post(route('store'), [
                'email' => 'nobody@example.com',
                'password' => '123',
            ])
            ->assertSee('Password must be at least 6 characters');
    }

    public function test_valid_user_can_login(): void
    {
        $user = User::factory()->staff()->create();

        $this->post(route('store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

}
