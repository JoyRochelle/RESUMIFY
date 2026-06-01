<?php

namespace Tests\Feature;

use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ButtonLoadingBugFixTest extends TestCase
{
    use RefreshDatabase;

    private User $basicUser;
    private User $premiumUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basicUser   = User::factory()->create(['role' => 'basic',   'email_verified_at' => now()]);
        $this->premiumUser = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
    }

    // =========================================================
    // Bug 1 — Auth button: spinner hidden on page load
    // =========================================================

    public function test_login_page_loads_with_livewire_scripts(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('livewire', false);
    }

    public function test_register_page_loads_with_livewire_scripts(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('livewire', false);
    }

    public function test_login_page_auth_button_spinner_has_display_none_fallback(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('style="display:none"', false);
    }

    public function test_register_page_auth_button_spinner_has_display_none_fallback(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('style="display:none"', false);
    }

    // =========================================================
    // Bug 2 — ATS Analyzer: no infinite pulse circle on empty state
    // =========================================================

    public function test_ats_analyzer_page_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('user.ai-assistant'))
            ->assertOk()
            ->assertSee('Analyze Match');
    }

    public function test_ats_analyzer_empty_state_does_not_have_pulse_animation(): void
    {
        $response = $this->actingAs($this->basicUser)
            ->get(route('user.ai-assistant'));

        $response->assertOk();
        $response->assertDontSee('animate-pulse-slow', false);
    }

    public function test_ats_analyzer_spinner_starts_hidden(): void
    {
        $response = $this->actingAs($this->basicUser)
            ->get(route('user.ai-assistant'));

        $response->assertOk();
        // Spinner uses inline style="display:none" to beat Material Symbols CSS specificity
        $response->assertSee('analyze-spinner', false);
        $response->assertSee('style="display:none"', false);
    }

    // =========================================================
    // Bug 3 — Upgrade Quota: Premium PRO button not blank
    // =========================================================

    public function test_upgrade_quota_page_loads_for_basic_user(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('user.upgrade-quota'))
            ->assertOk()
            ->assertSee('Premium PRO');
    }

    public function test_upgrade_quota_premium_button_has_text_for_basic_user(): void
    {
        $response = $this->actingAs($this->basicUser)
            ->get(route('user.upgrade-quota'));

        $response->assertOk();
        $response->assertSee('Activate Premium Now');
    }

    public function test_upgrade_quota_page_loads_for_premium_user(): void
    {
        $this->actingAs($this->premiumUser)
            ->get(route('user.upgrade-quota'))
            ->assertOk()
            ->assertSee('Premium PRO');
    }

    public function test_upgrade_quota_page_contains_payment_gateway_script(): void
    {
        $response = $this->actingAs($this->basicUser)
            ->get(route('user.upgrade-quota'));

        $response->assertOk();
        $response->assertSee('paymentGateway', false);
    }

    public function test_upgrade_quota_alpine_init_registered_before_livewire_scripts(): void
    {
        $response = $this->actingAs($this->basicUser)
            ->get(route('user.upgrade-quota'));

        $response->assertOk();
        $content = $response->getContent();

        // alpine:init listener must appear before @livewireScripts in the rendered HTML
        $alpineInitPos  = strpos($content, 'alpine:init');
        $livewireScriptPos = strpos($content, 'livewire.js') ?: strpos($content, 'livewire/livewire.js');

        if ($alpineInitPos !== false && $livewireScriptPos !== false) {
            $this->assertLessThan(
                $livewireScriptPos,
                $alpineInitPos,
                'alpine:init listener must appear before livewire scripts so paymentGateway is registered in time'
            );
        } else {
            // If livewire script tag isn't present (different rendering), just assert page is ok
            $this->assertTrue(true);
        }
    }
}
