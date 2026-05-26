<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AiUsageLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotaSystemTest extends TestCase
{
    use RefreshDatabase;

    // ────────────────────────────────────────────────
    // QuotaMiddleware: Basic user with remaining credits
    // ────────────────────────────────────────────────

    public function test_basic_user_with_remaining_quota_can_access_ai_route(): void
    {
        $user = User::factory()->create([
            'role'          => 'basic',
            'ai_quota_used' => 0,
        ]);

        // Use a real AI route that has the ai.quota middleware
        // Since ai.quota isn't applied to routes yet (that's Branch 3),
        // we test the middleware directly via a test route
        $this->app['router']->post('/_test/ai-action', function () {
            return response()->json(['success' => true]);
        })->middleware(['web', 'auth', 'ai.quota:1']);

        $response = $this->actingAs($user)->postJson('/_test/ai-action');
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    // ────────────────────────────────────────────────
    // QuotaMiddleware: Basic user with exhausted credits
    // ────────────────────────────────────────────────

    public function test_basic_user_with_exhausted_quota_gets_402(): void
    {
        $user = User::factory()->create([
            'role'          => 'basic',
            'ai_quota_used' => 5, // Free limit = 5
        ]);

        $this->app['router']->post('/_test/ai-action', function () {
            return response()->json(['success' => true]);
        })->middleware(['web', 'auth', 'ai.quota:1']);

        $response = $this->actingAs($user)->postJson('/_test/ai-action');
        $response->assertStatus(402)
                 ->assertJson([
                     'error'   => 'quota_exceeded',
                     'remaining' => 0,
                 ]);
    }

    // ────────────────────────────────────────────────
    // QuotaMiddleware: Multi-credit check (e.g., generate-versions = 3)
    // ────────────────────────────────────────────────

    public function test_basic_user_blocked_when_insufficient_credits_for_multi_credit_action(): void
    {
        $user = User::factory()->create([
            'role'          => 'basic',
            'ai_quota_used' => 3, // 5 - 3 = 2 remaining, but needs 3
        ]);

        $this->app['router']->post('/_test/ai-versions', function () {
            return response()->json(['success' => true]);
        })->middleware(['web', 'auth', 'ai.quota:3']);

        $response = $this->actingAs($user)->postJson('/_test/ai-versions');
        $response->assertStatus(402)
                 ->assertJson(['error' => 'quota_exceeded']);
    }

    public function test_basic_user_passes_when_exact_credits_remaining(): void
    {
        $user = User::factory()->create([
            'role'          => 'basic',
            'ai_quota_used' => 2, // 5 - 2 = 3 remaining, needs exactly 3
        ]);

        $this->app['router']->post('/_test/ai-versions', function () {
            return response()->json(['success' => true]);
        })->middleware(['web', 'auth', 'ai.quota:3']);

        $response = $this->actingAs($user)->postJson('/_test/ai-versions');
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    // ────────────────────────────────────────────────
    // QuotaMiddleware: Premium users bypass quota
    // ────────────────────────────────────────────────

    public function test_premium_user_bypasses_quota_check(): void
    {
        $user = User::factory()->create([
            'role'          => 'premium',
            'ai_quota_used' => 999, // Even fully exhausted, premium bypasses
        ]);

        $this->app['router']->post('/_test/ai-action', function () {
            return response()->json(['success' => true]);
        })->middleware(['web', 'auth', 'ai.quota:1']);

        $response = $this->actingAs($user)->postJson('/_test/ai-action');
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    // ────────────────────────────────────────────────
    // QuotaMiddleware: Admin users bypass quota
    // ────────────────────────────────────────────────

    public function test_admin_user_bypasses_quota_check(): void
    {
        $user = User::factory()->create([
            'role'          => 'admin',
            'ai_quota_used' => 999,
        ]);

        $this->app['router']->post('/_test/ai-action', function () {
            return response()->json(['success' => true]);
        })->middleware(['web', 'auth', 'ai.quota:1']);

        $response = $this->actingAs($user)->postJson('/_test/ai-action');
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    // ────────────────────────────────────────────────
    // User Model: hasQuotaRemaining helper
    // ────────────────────────────────────────────────

    public function test_user_has_quota_remaining_returns_true_when_credits_available(): void
    {
        $user = User::factory()->create([
            'role'          => 'basic',
            'ai_quota_used' => 3,
        ]);

        $this->assertTrue($user->hasQuotaRemaining(1));
        $this->assertTrue($user->hasQuotaRemaining(2));
        $this->assertFalse($user->hasQuotaRemaining(3));
    }

    // ────────────────────────────────────────────────
    // User Model: getQuotaRemaining / getQuotaLimit
    // ────────────────────────────────────────────────

    public function test_user_quota_limit_matches_config(): void
    {
        $basicUser = User::factory()->create(['role' => 'basic', 'ai_quota_used' => 0]);
        $premiumUser = User::factory()->create(['role' => 'premium', 'ai_quota_used' => 0]);

        $this->assertEquals(config('quota.basic'), $basicUser->getQuotaLimit());
        $this->assertEquals(config('quota.premium'), $premiumUser->getQuotaLimit());

        $this->assertEquals(config('quota.basic'), $basicUser->getQuotaRemaining());
        $this->assertEquals(config('quota.premium'), $premiumUser->getQuotaRemaining());
    }

    // ────────────────────────────────────────────────
    // AiUsageLog Model: creation
    // ────────────────────────────────────────────────

    public function test_ai_usage_log_can_be_created(): void
    {
        $user = User::factory()->create();

        $log = AiUsageLog::create([
            'user_id'     => $user->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 150,
            'cost_usd'    => 0.000150,
        ]);

        $this->assertDatabaseHas('ai_usage_logs', [
            'user_id'     => $user->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 150,
        ]);

        $this->assertEquals($user->id, $log->user->id);
    }

    // ────────────────────────────────────────────────
    // quota:reset command
    // ────────────────────────────────────────────────

    public function test_quota_reset_command_resets_expired_users(): void
    {
        // User whose quota should be reset (reset_at is 2 months ago)
        $expiredUser = User::factory()->create([
            'role'              => 'basic',
            'ai_quota_used'     => 4,
            'ai_quota_reset_at' => now()->subMonths(2),
        ]);

        // User whose quota should NOT be reset (reset_at is recent)
        $recentUser = User::factory()->create([
            'role'              => 'basic',
            'ai_quota_used'     => 3,
            'ai_quota_reset_at' => now()->subDays(5),
        ]);

        // User with null reset_at and used quota — should be reset
        $nullResetUser = User::factory()->create([
            'role'              => 'basic',
            'ai_quota_used'     => 2,
            'ai_quota_reset_at' => null,
        ]);

        $this->artisan('quota:reset')
             ->expectsOutputToContain('Reset AI quota for 2 user(s)')
             ->assertExitCode(0);

        $expiredUser->refresh();
        $this->assertEquals(0, $expiredUser->ai_quota_used);
        $this->assertNotNull($expiredUser->ai_quota_reset_at);

        $recentUser->refresh();
        $this->assertEquals(3, $recentUser->ai_quota_used); // unchanged

        $nullResetUser->refresh();
        $this->assertEquals(0, $nullResetUser->ai_quota_used);
    }

    public function test_quota_reset_skips_users_with_zero_usage(): void
    {
        $user = User::factory()->create([
            'role'              => 'basic',
            'ai_quota_used'     => 0,
            'ai_quota_reset_at' => now()->subMonths(2),
        ]);

        $this->artisan('quota:reset')
             ->expectsOutputToContain('Reset AI quota for 0 user(s)')
             ->assertExitCode(0);

        $user->refresh();
        $this->assertEquals(0, $user->ai_quota_used);
    }
}
