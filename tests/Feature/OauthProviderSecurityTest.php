<?php

namespace Tests\Feature;

use App\Models\OauthProvider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class OauthProviderSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_oauth_provider_token_is_not_serialized_to_arrays_or_json(): void
    {
        $user = User::factory()->create();

        $oauthProvider = OauthProvider::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-user-123',
        ]);
        $oauthProvider->updateToken('plain-oauth-token');

        $this->assertArrayNotHasKey('token', $oauthProvider->toArray());
        $this->assertStringNotContainsString('plain-oauth-token', $oauthProvider->toJson());
        $this->assertStringNotContainsString('token', $oauthProvider->toJson());
    }

    public function test_oauth_provider_token_is_encrypted_before_storage(): void
    {
        $user = User::factory()->create();
        $plainToken = 'plain-oauth-token';

        $oauthProvider = OauthProvider::create([
            'user_id' => $user->id,
            'provider' => 'linkedin-openid',
            'provider_id' => 'linkedin-user-123',
        ]);
        $oauthProvider->updateToken($plainToken);

        $storedToken = DB::table('oauth_providers')
            ->where('id', $oauthProvider->id)
            ->value('token');

        $this->assertNotSame($plainToken, $storedToken);
        $this->assertSame($plainToken, $oauthProvider->refresh()->token);
    }

    public function test_oauth_provider_token_cannot_be_mass_assigned(): void
    {
        $user = User::factory()->create();

        $oauthProvider = OauthProvider::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => 'google-user-123',
            'token' => 'mass-assigned-token',
        ]);

        $this->assertNull($oauthProvider->refresh()->token);
    }

    public function test_google_oauth_callback_links_a_provider_with_an_encrypted_token(): void
    {
        $this->mockSocialiteUser('google', 'google-user-123', 'new@example.com', 'google-token');

        $response = $this->get(route('social.callback', ['provider' => 'google']));

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $oauthProvider = OauthProvider::where('provider', 'google')
            ->where('provider_id', 'google-user-123')
            ->firstOrFail();

        $storedToken = DB::table('oauth_providers')
            ->where('id', $oauthProvider->id)
            ->value('token');

        $this->assertNotSame('google-token', $storedToken);
        $this->assertSame('google-token', $oauthProvider->token);
    }

    public function test_linkedin_oauth_callback_updates_an_existing_provider_token(): void
    {
        $user = User::factory()->create(['email' => 'existing@example.com']);
        $oauthProvider = $user->oauthProviders()->create([
            'provider' => 'linkedin-openid',
            'provider_id' => 'linkedin-user-123',
        ]);
        $oauthProvider->updateToken('old-linkedin-token');

        $this->mockSocialiteUser('linkedin-openid', 'linkedin-user-123', 'existing@example.com', 'new-linkedin-token');

        $response = $this->get(route('social.callback', ['provider' => 'linkedin-openid']));

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $storedToken = DB::table('oauth_providers')
            ->where('id', $oauthProvider->id)
            ->value('token');

        $this->assertNotSame('new-linkedin-token', $storedToken);
        $this->assertSame('new-linkedin-token', $oauthProvider->refresh()->token);
    }

    public function test_oauth_callback_replaces_a_legacy_plaintext_token_without_decrypting_it(): void
    {
        $user = User::factory()->create(['email' => 'legacy@example.com']);
        $oauthProvider = $user->oauthProviders()->create([
            'provider' => 'google',
            'provider_id' => 'legacy-google-user-123',
        ]);

        DB::table('oauth_providers')
            ->where('id', $oauthProvider->id)
            ->update(['token' => 'legacy-plaintext-token']);

        $this->mockSocialiteUser('google', 'legacy-google-user-123', 'legacy@example.com', 'rotated-google-token');

        $response = $this->get(route('social.callback', ['provider' => 'google']));

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);

        $storedToken = DB::table('oauth_providers')
            ->where('id', $oauthProvider->id)
            ->value('token');

        $this->assertNotSame('legacy-plaintext-token', $storedToken);
        $this->assertNotSame('rotated-google-token', $storedToken);
        $this->assertSame('rotated-google-token', $oauthProvider->refresh()->token);
    }

    private function mockSocialiteUser(string $provider, string $providerId, string $email, string $token): void
    {
        $socialiteUser = (new SocialiteUser())->map([
            'id' => $providerId,
            'name' => 'OAuth User',
            'email' => $email,
            'avatar' => 'https://example.com/avatar.png',
        ]);
        $socialiteUser->setToken($token);

        $providerMock = Mockery::mock();
        $providerMock->shouldReceive('user')
            ->once()
            ->andReturn($socialiteUser);

        Socialite::shouldReceive('driver')
            ->once()
            ->with($provider)
            ->andReturn($providerMock);
    }
}
