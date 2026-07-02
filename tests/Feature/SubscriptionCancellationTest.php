<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_basic_user_sees_visible_premium_price_and_purchase_action(): void
    {
        $user = User::factory()->create([
            'role' => 'basic',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('user.upgrade-quota'));

        $response->assertOk();
        $response->assertSee('Rp 49.000');
        $response->assertSee('/ month');
        $response->assertSee('data-plan-action="purchase-premium"', false);
        $response->assertDontSee('data-plan-action="purchase-premium-summary"', false);
        $response->assertSee('Activate Premium Now');

        $content = $response->getContent();
        preg_match_all('/<button[^>]*data-plan-action="purchase-premium"/', $content, $matches);
        $this->assertCount(1, $matches[0]);
        $this->assertStringContainsString('addEventListener(\'click\', handlePremiumPurchase)', $content);
        $this->assertStringContainsString('fetch(\'' . route('payment.create') . '\'', $content);
        $this->assertStringNotContainsString('x-text="isProcessing', $content);
    }

    public function test_premium_user_can_cancel_plan_without_losing_access_until_period_end(): void
    {
        $user = User::factory()->create([
            'role' => 'premium',
            'email_verified_at' => now(),
        ]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan' => 'premium',
            'status' => 'active',
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(20),
        ]);

        $this->actingAs($user)
            ->post(route('subscription.cancel'))
            ->assertRedirect(route('user.upgrade-quota'))
            ->assertSessionHas('success');

        $subscription->refresh();
        $this->assertEquals('cancelled', $subscription->status);
        $this->assertTrue($subscription->ends_at->isFuture());
        $this->assertEquals('premium', $user->fresh()->role);
    }

    public function test_premium_user_sees_cancel_plan_action_on_upgrade_page(): void
    {
        $user = User::factory()->create([
            'role' => 'premium',
            'email_verified_at' => now(),
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan' => 'premium',
            'status' => 'active',
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->addDays(28),
        ]);

        $response = $this->actingAs($user)->get(route('user.upgrade-quota'));

        $response->assertOk();
        $response->assertSee('Cancel Plan');
        $response->assertSee('action="' . route('subscription.cancel') . '"', false);
    }

    public function test_upgrade_page_prefers_current_subscription_when_rendering_cancel_plan_action(): void
    {
        $user = User::factory()->create([
            'role' => 'premium',
            'email_verified_at' => now(),
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan' => 'premium',
            'status' => 'active',
            'starts_at' => now()->subDays(3),
            'ends_at' => now()->addDays(27),
            'created_at' => now()->subDay(),
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'plan' => 'premium',
            'status' => 'expired',
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subMonth(),
            'created_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('user.upgrade-quota'));

        $response->assertOk();
        $response->assertSee('Cancel Plan');
    }

    public function test_payment_script_falls_back_to_midtrans_redirect_when_snap_popup_is_not_ready(): void
    {
        $user = User::factory()->create([
            'role' => 'basic',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('user.upgrade-quota'));

        $response->assertOk();
        $response->assertSee('window.snap.pay', false);
        $response->assertSee('window.location.href = data.redirect_url', false);
        $response->assertSee('Midtrans payment popup is not ready', false);
    }

    public function test_expired_cancelled_subscription_downgrades_user_after_period_end(): void
    {
        $user = User::factory()->create([
            'role' => 'premium',
            'email_verified_at' => now(),
            'ai_quota_used' => 4,
        ]);

        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan' => 'premium',
            'status' => 'cancelled',
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subDay(),
        ]);

        $this->artisan('subscriptions:expire-cancelled')
            ->expectsOutputToContain('Expired 1 cancelled subscription(s).')
            ->assertSuccessful();

        $this->assertEquals('basic', $user->fresh()->role);
        $this->assertEquals('expired', $subscription->fresh()->status);
    }
}
