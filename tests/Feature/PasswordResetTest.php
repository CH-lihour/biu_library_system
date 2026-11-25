<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private const SENT = 'If that email exists, a reset link was sent.';

    private const BAD_LINK = 'This link is invalid or has expired.';

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    private function requestLink(string $email): TestResponse
    {
        return $this->from(route('password.request'))->post(route('password.email'), ['email' => $email]);
    }

    private function resetPassword(User $user, string $token, array $changes = []): TestResponse
    {
        return $this->from(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->post(route('password.update'), $changes + [
                'token' => $token,
                'email' => $user->email,
                'password' => 'new-secret-123',
                'password_confirmation' => 'new-secret-123',
            ]);
    }

    // === Forgot password ===

    public function test_login_page_links_to_the_forgot_password_page(): void
    {
        $this->get(route('login'))->assertOk()->assertSee(route('password.request'));
        $this->get(route('password.request'))->assertOk()->assertSee('Send reset link');
    }

    public function test_known_email_gets_a_link_to_the_reset_page(): void
    {
        $user = User::factory()->create(['email' => 'dara@example.com']);

        $this->requestLink(' Dara@Example.com ')
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('success', self::SENT);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $link = $notification->toMail($user)->actionUrl;

            $this->assertSame(route('password.reset', ['token' => $notification->token, 'email' => $user->email]), $link);
            $this->get($link)
                ->assertOk()
                ->assertSee('value="'.$notification->token.'"', false)
                ->assertSee('value="dara@example.com"', false);

            return true;
        });
        $this->assertDatabaseCount('password_reset_tokens', 1);
    }

    public function test_unknown_email_gets_the_same_message_but_no_link(): void
    {
        $this->requestLink('nobody@example.com')
            ->assertRedirect(route('password.request'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success', self::SENT);

        Notification::assertNothingSent();
        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function test_deleted_user_gets_no_link(): void
    {
        $user = User::factory()->create();
        $user->delete();

        $this->requestLink($user->email)->assertSessionHas('success', self::SENT);

        Notification::assertNothingSent();
    }

    public function test_asking_again_too_soon_sends_only_one_link(): void
    {
        $user = User::factory()->create();

        $this->requestLink($user->email);
        $this->requestLink($user->email)->assertSessionHas('success', self::SENT);

        Notification::assertSentToTimes($user, ResetPassword::class, 1);
    }

    public function test_forgot_password_email_rules(): void
    {
        $this->requestLink('')->assertSessionHasErrors(['email' => 'Email is required']);
        $this->requestLink('abc')->assertSessionHasErrors(['email' => 'Please enter a valid email']);

        Notification::assertNothingSent();
    }

    public function test_forgot_password_form_is_rate_limited(): void
    {
        foreach (range(1, 6) as $try) {
            $this->requestLink('nobody@example.com')->assertRedirect();
        }

        $this->requestLink('nobody@example.com')->assertTooManyRequests();
    }

    // === Reset password ===

    public function test_user_resets_the_password_and_signs_in_with_it(): void
    {
        $user = User::factory()->staff()->create();
        $rememberToken = $user->remember_token;

        $this->followingRedirects();
        $this->resetPassword($user, Password::createToken($user))
            ->assertOk()
            ->assertSee('Your password was changed. Please sign in.');

        $this->assertGuest();
        $this->assertTrue(Hash::check('new-secret-123', $user->fresh()->password));
        $this->assertNotSame($rememberToken, $user->fresh()->remember_token);
        $this->assertDatabaseCount('password_reset_tokens', 0);

        $this->post(route('store'), ['email' => $user->email, 'password' => 'password']);
        $this->assertGuest();

        $this->post(route('store'), ['email' => $user->email, 'password' => 'new-secret-123']);
        $this->assertAuthenticatedAs($user);
    }

    public function test_successful_reset_goes_to_the_login_page(): void
    {
        $user = User::factory()->create();

        $this->resetPassword($user, Password::createToken($user))
            ->assertRedirect(route('login'))
            ->assertSessionHas('success', 'Your password was changed. Please sign in.');
    }

    public function test_wrong_token_is_rejected(): void
    {
        $user = User::factory()->create();
        Password::createToken($user);

        $this->resetPassword($user, 'wrong-token')->assertSessionHasErrors(['email' => self::BAD_LINK]);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_token_of_another_user_is_rejected(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Password::createToken($user);

        $this->resetPassword($user, Password::createToken($other))->assertSessionHasErrors(['email' => self::BAD_LINK]);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $this->assertTrue(Hash::check('password', $other->fresh()->password));
    }

    public function test_expired_token_is_rejected(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->travel(61)->minutes();

        $this->resetPassword($user, $token)->assertSessionHasErrors(['email' => self::BAD_LINK]);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_link_works_only_once(): void
    {
        $user = User::factory()->create();
        $token = Password::createToken($user);

        $this->resetPassword($user, $token)->assertSessionHasNoErrors();
        $this->resetPassword($user, $token, ['password' => 'another-123', 'password_confirmation' => 'another-123'])
            ->assertSessionHasErrors(['email' => self::BAD_LINK]);

        $this->assertTrue(Hash::check('new-secret-123', $user->fresh()->password));
    }

    #[DataProvider('invalidResetForms')]
    public function test_reset_password_rules(array $changes, string $field, string $message): void
    {
        $user = User::factory()->create();

        $this->resetPassword($user, Password::createToken($user), $changes)
            ->assertSessionHasErrors([$field => $message]);

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $this->assertDatabaseCount('password_reset_tokens', 1);
    }

    public static function invalidResetForms(): array
    {
        return [
            'no token' => [['token' => ''], 'token', 'This reset link is not valid. Please request a new one.'],
            'no email' => [['email' => ''], 'email', 'Email is required'],
            'bad email' => [['email' => 'abc'], 'email', 'Please enter a valid email'],
            'no password' => [['password' => '', 'password_confirmation' => ''], 'password', 'Password is required'],
            'short password' => [['password' => '1234567', 'password_confirmation' => '1234567'], 'password', 'The password field must be at least 8 characters.'],
            'passwords differ' => [['password_confirmation' => 'other-secret-123'], 'password', 'The passwords do not match.'],
        ];
    }

    // === Access ===

    public function test_signed_in_user_cannot_open_the_pages(): void
    {
        $this->actingAs(User::factory()->staff()->create());

        $this->get(route('password.request'))->assertRedirect();
        $this->get(route('password.reset', ['token' => 'any']))->assertRedirect();
    }
}
