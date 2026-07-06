<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_english_validation_messages_by_default(): void
    {
        $response = $this->post('/register', [
            'email' => 'new-user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertInvalid(['name' => 'is required']);
    }

    public function test_guest_can_switch_locale_and_it_persists_in_session(): void
    {
        $this->post('/locale/id')->assertRedirect();

        $response = $this->post('/register', [
            'email' => 'new-user@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertInvalid(['name' => 'wajib diisi']);
    }

    public function test_switching_to_an_unsupported_locale_is_rejected(): void
    {
        $this->post('/locale/fr')->assertNotFound();
    }

    public function test_authenticated_user_locale_preference_is_persisted_to_the_database(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'locale' => null]);

        $this->actingAs($user)->post('/locale/id')->assertRedirect();

        $this->assertSame('id', $user->fresh()->locale);
    }

    public function test_authenticated_user_database_locale_takes_priority_over_session(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'locale' => 'en']);

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'id'])
            ->delete('/profile');

        $response->assertInvalid(['password' => 'is required']);
    }

    public function test_authenticated_user_without_saved_locale_falls_back_to_session(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'locale' => null]);

        $response = $this->actingAs($user)
            ->withSession(['locale' => 'id'])
            ->delete('/profile');

        $response->assertInvalid(['password' => 'wajib diisi']);
    }

    public function test_locale_preference_persists_after_logout_and_login(): void
    {
        $user = User::factory()->create([
            'role' => 'basic',
            'locale' => null,
            'password' => Hash::make('i-love-laravel'),
        ]);

        $this->actingAs($user)->post('/locale/id')->assertRedirect();

        $this->post('/logout');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'i-love-laravel',
        ]);

        $response = $this->delete('/profile');

        $response->assertInvalid(['password' => 'wajib diisi']);
    }
}
