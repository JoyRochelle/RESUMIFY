<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkedInInDevelopmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_linkedin_redirect_is_blocked_with_development_notice(): void
    {
        $response = $this->get(route('social.redirect', ['provider' => 'linkedin-openid']));

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('oauth');
    }

    public function test_login_page_shows_linkedin_in_development_badge(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee(__('messages.auth.social.in_development'));
    }

    public function test_unknown_provider_still_returns_404(): void
    {
        $response = $this->get(route('social.redirect', ['provider' => 'facebook']));

        $response->assertNotFound();
    }
}
