<?php

namespace Tests\Feature;

use App\Models\AiUsageLog;
use App\Models\User;
use App\Services\AiCreditReservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Throwable;

class QuotaSystemTest extends TestCase
{
    use RefreshDatabase;

    // ────────────────────────────────────────────────
    // Atomic AI credit reservation boundary
    // ────────────────────────────────────────────────

    public function test_basic_user_with_one_remaining_credit_cannot_spend_two_parallel_requests(): void
    {
        $quotaLimit = (int) config('quota.basic');
        $user = User::factory()->create([
            'role' => 'basic',
            'ai_quota_used' => $quotaLimit - 1,
        ]);

        if ($this->quotaBoundaryExists()) {
            $firstReservation = $this->reserveAiCredits($user->id, 1);
            $secondReservation = $this->reserveAiCredits($user->id, 1);
        } else {
            $firstRequestSnapshot = User::query()->findOrFail($user->id);
            $secondRequestSnapshot = User::query()->findOrFail($user->id);

            $firstReservation = $this->legacyNonAtomicReservationFromSnapshot($firstRequestSnapshot, 1);
            $secondReservation = $this->legacyNonAtomicReservationFromSnapshot($secondRequestSnapshot, 1);
        }

        $successfulReservations = collect([$firstReservation, $secondReservation])
            ->where('reserved', true)
            ->count();

        $this->assertSame(
            1,
            $successfulReservations,
            'Atomic quota reservation must allow only one of two competing requests when one credit remains.'
        );
        $this->assertSame(
            $quotaLimit,
            $user->fresh()->ai_quota_used,
            'Atomic quota reservation must not debit beyond the configured basic quota limit.'
        );
    }

    public function test_refunding_same_reservation_twice_does_not_exceed_original_balance(): void
    {
        $quotaLimit = (int) config('quota.basic');
        $user = User::factory()->create([
            'role' => 'basic',
            'ai_quota_used' => $quotaLimit - 1,
        ]);

        $reservation = $this->reserveAiCredits($user->id, 1);
        $this->assertTrue($reservation['reserved']);
        $this->assertSame($quotaLimit, $user->fresh()->ai_quota_used);

        $this->refundAiCredits($reservation);
        $this->refundAiCredits($reservation);

        $reconstructedReservation = AiCreditReservation::makeReserved(
            id: $reservation['reservation']->id,
            userId: $reservation['reservation']->userId,
            credits: $reservation['reservation']->credits,
            previousUsage: $reservation['reservation']->previousUsage,
            usageAfterReservation: $reservation['reservation']->usageAfterReservation,
            quotaLimit: $reservation['reservation']->quotaLimit,
            context: $reservation['reservation']->context,
        );
        $this->refundAiCredits([
            ...$reservation,
            'reservation' => $reconstructedReservation,
        ]);

        $this->assertSame(
            $quotaLimit - 1,
            $user->fresh()->ai_quota_used,
            'Refunding the same reservation twice must restore only the credits from that reservation once.'
        );
    }

    public function test_admin_users_bypass_quota_accounting(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'ai_quota_used' => 999,
        ]);

        $reservation = $this->reserveAiCredits($user->id, 3);

        $this->assertTrue($reservation['bypassed']);
        $this->assertFalse($reservation['reserved']);
        $this->assertSame(
            999,
            $user->fresh()->ai_quota_used,
            'Admin users must not have ai_quota_used changed by AI credit reservation.'
        );
    }

    public function test_premium_users_are_metered_against_premium_quota(): void
    {
        $quotaLimit = (int) config('quota.premium');
        $user = User::factory()->create([
            'role' => 'premium',
            'ai_quota_used' => $quotaLimit - 1,
        ]);

        $reservation = $this->reserveAiCredits($user->id, 1);

        $this->assertTrue($reservation['reserved']);
        $this->assertFalse($reservation['bypassed']);
        $this->assertSame(
            $quotaLimit,
            $user->fresh()->ai_quota_used,
            'Premium users must have ai_quota_used incremented like basic users, capped at their own (higher) limit.'
        );
    }

    public function test_premium_user_denied_reservation_once_premium_quota_exhausted(): void
    {
        $quotaLimit = (int) config('quota.premium');
        $user = User::factory()->create([
            'role' => 'premium',
            'ai_quota_used' => $quotaLimit,
        ]);

        $reservation = $this->reserveAiCredits($user->id, 1);

        $this->assertFalse($reservation['reserved']);
        $this->assertFalse($reservation['bypassed']);
        $this->assertSame(
            $quotaLimit,
            $user->fresh()->ai_quota_used,
            'A denied reservation must not change ai_quota_used.'
        );
    }

    // ────────────────────────────────────────────────
    // QuotaMiddleware: Basic user with remaining credits
    // ────────────────────────────────────────────────

    public function test_basic_user_with_remaining_quota_can_access_ai_route(): void
    {
        $user = User::factory()->create([
            'role' => 'basic',
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
            'role' => 'basic',
            'ai_quota_used' => 5, // Free limit = 5
        ]);

        $this->app['router']->post('/_test/ai-action', function () {
            return response()->json(['success' => true]);
        })->middleware(['web', 'auth', 'ai.quota:1']);

        $response = $this->actingAs($user)->postJson('/_test/ai-action');
        $response->assertStatus(402)
            ->assertJson([
                'error' => 'quota_exceeded',
                'remaining' => 0,
            ]);
    }

    // ────────────────────────────────────────────────
    // QuotaMiddleware: Multi-credit check (e.g., generate-versions = 3)
    // ────────────────────────────────────────────────

    public function test_basic_user_blocked_when_insufficient_credits_for_multi_credit_action(): void
    {
        $user = User::factory()->create([
            'role' => 'basic',
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
            'role' => 'basic',
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
    // QuotaMiddleware: Premium users are metered against their own limit
    // ────────────────────────────────────────────────

    public function test_premium_user_with_remaining_quota_can_access_ai_route(): void
    {
        $user = User::factory()->create([
            'role' => 'premium',
            'ai_quota_used' => 0,
        ]);

        $this->app['router']->post('/_test/ai-action', function () {
            return response()->json(['success' => true]);
        })->middleware(['web', 'auth', 'ai.quota:1']);

        $response = $this->actingAs($user)->postJson('/_test/ai-action');
        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_premium_user_with_exhausted_quota_gets_402(): void
    {
        $quotaLimit = (int) config('quota.premium');
        $user = User::factory()->create([
            'role' => 'premium',
            'ai_quota_used' => $quotaLimit,
        ]);

        $this->app['router']->post('/_test/ai-action', function () {
            return response()->json(['success' => true]);
        })->middleware(['web', 'auth', 'ai.quota:1']);

        $response = $this->actingAs($user)->postJson('/_test/ai-action');
        $response->assertStatus(402)
            ->assertJson(['error' => 'quota_exceeded']);
    }

    // ────────────────────────────────────────────────
    // QuotaMiddleware: Admin users bypass quota
    // ────────────────────────────────────────────────

    public function test_admin_user_bypasses_quota_check(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
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
            'role' => 'basic',
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
    // User Model: getResumeLimit follows plans.resume_limits config
    // (which is populated from RESUME_LIMIT_BASIC / RESUME_LIMIT_PREMIUM env vars)
    // ────────────────────────────────────────────────

    public function test_resume_limit_follows_plans_config(): void
    {
        config(['plans.resume_limits.basic' => 2, 'plans.resume_limits.premium' => 10]);

        $basicUser = User::factory()->create(['role' => 'basic']);
        $premiumUser = User::factory()->create(['role' => 'premium']);

        $this->assertSame(2, $basicUser->getResumeLimit());
        $this->assertSame(10, $premiumUser->getResumeLimit());
    }

    public function test_premium_resume_limit_is_unlimited_when_env_value_is_empty(): void
    {
        config(['plans.resume_limits.premium' => null]);

        $premiumUser = User::factory()->create(['role' => 'premium']);

        $this->assertNull($premiumUser->getResumeLimit());
        $this->assertTrue($premiumUser->canCreateResume());
    }

    // ────────────────────────────────────────────────
    // AiUsageLog Model: creation
    // ────────────────────────────────────────────────

    public function test_ai_usage_log_can_be_created(): void
    {
        $user = User::factory()->create();

        $log = AiUsageLog::create([
            'user_id' => $user->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 150,
            'cost_usd' => 0.000150,
        ]);

        $this->assertDatabaseHas('ai_usage_logs', [
            'user_id' => $user->id,
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
            'role' => 'basic',
            'ai_quota_used' => 4,
            'ai_quota_reset_at' => now()->subMonths(2),
        ]);

        // User whose quota should NOT be reset (reset_at is recent)
        $recentUser = User::factory()->create([
            'role' => 'basic',
            'ai_quota_used' => 3,
            'ai_quota_reset_at' => now()->subDays(5),
        ]);

        // User with null reset_at and used quota — should be reset
        $nullResetUser = User::factory()->create([
            'role' => 'basic',
            'ai_quota_used' => 2,
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
            'role' => 'basic',
            'ai_quota_used' => 0,
            'ai_quota_reset_at' => now()->subMonths(2),
        ]);

        $this->artisan('quota:reset')
            ->expectsOutputToContain('Reset AI quota for 0 user(s)')
            ->assertExitCode(0);

        $user->refresh();
        $this->assertEquals(0, $user->ai_quota_used);
    }

    /**
     * Reserve credits through the future canonical boundary. Until that
     * boundary exists, this intentionally falls back to the current stale
     * read-then-increment pattern so the race regression is visible in RED.
     *
     * @return array{reserved: bool, bypassed: bool, reservation: mixed, user_id: string, credits: int}
     */
    private function reserveAiCredits(string $userId, int $credits): array
    {
        $serviceClass = 'App\\Services\\AiCreditService';
        $actionClass = 'App\\Actions\\Quota\\ReserveAiCreditsAction';

        if (class_exists($serviceClass)) {
            $reservation = app($serviceClass)->reserve(
                User::query()->findOrFail($userId),
                $credits,
                'quota_system_test'
            );

            return $this->normalizeReservation($reservation, $userId, $credits);
        }

        if (class_exists($actionClass)) {
            $reservation = app($actionClass)->execute(
                User::query()->findOrFail($userId),
                $credits,
                'quota_system_test'
            );

            return $this->normalizeReservation($reservation, $userId, $credits);
        }

        return $this->legacyNonAtomicReservation($userId, $credits);
    }

    private function quotaBoundaryExists(): bool
    {
        return class_exists('App\\Services\\AiCreditService')
            || class_exists('App\\Actions\\Quota\\ReserveAiCreditsAction');
    }

    /**
     * @param  array{reserved: bool, bypassed: bool, reservation: mixed, user_id: string, credits: int}  $reservation
     */
    private function refundAiCredits(array $reservation): void
    {
        $serviceClass = 'App\\Services\\AiCreditService';

        $this->assertTrue(
            class_exists($serviceClass) || is_object($reservation['reservation']),
            'Expected AiCreditService or a reservation object with refund behavior for idempotent refunds.'
        );

        if (class_exists($serviceClass)) {
            app($serviceClass)->refund($reservation['reservation']);

            return;
        }

        $this->assertTrue(
            method_exists($reservation['reservation'], 'refund'),
            'Reservation result object must expose refund behavior when AiCreditService is not used.'
        );

        $reservation['reservation']->refund();
    }

    /**
     * @return array{reserved: bool, bypassed: bool, reservation: mixed, user_id: string, credits: int}
     */
    private function normalizeReservation(mixed $reservation, string $userId, int $credits): array
    {
        return [
            'reserved' => $this->readBooleanResult($reservation, ['reserved', 'isReserved']),
            'bypassed' => $this->readBooleanResult($reservation, ['bypassed', 'isBypassed']),
            'reservation' => $reservation,
            'user_id' => $userId,
            'credits' => $credits,
        ];
    }

    private function readBooleanResult(mixed $reservation, array $names): bool
    {
        foreach ($names as $name) {
            if (is_object($reservation) && method_exists($reservation, $name)) {
                return (bool) $reservation->{$name}();
            }

            if (is_object($reservation) && property_exists($reservation, $name)) {
                return (bool) $reservation->{$name};
            }

            if (is_array($reservation) && array_key_exists($name, $reservation)) {
                return (bool) $reservation[$name];
            }
        }

        return false;
    }

    /**
     * @return array{reserved: bool, bypassed: bool, reservation: null, user_id: string, credits: int}
     */
    private function legacyNonAtomicReservation(string $userId, int $credits): array
    {
        $userSnapshot = User::query()->findOrFail($userId);

        return $this->legacyNonAtomicReservationFromSnapshot($userSnapshot, $credits);
    }

    /**
     * @return array{reserved: bool, bypassed: bool, reservation: null, user_id: string, credits: int}
     */
    private function legacyNonAtomicReservationFromSnapshot(User $userSnapshot, int $credits): array
    {
        if ($userSnapshot->isAdmin()) {
            return [
                'reserved' => false,
                'bypassed' => true,
                'reservation' => null,
                'user_id' => $userSnapshot->id,
                'credits' => $credits,
            ];
        }

        if (! $userSnapshot->hasQuotaRemaining($credits)) {
            return [
                'reserved' => false,
                'bypassed' => false,
                'reservation' => null,
                'user_id' => $userSnapshot->id,
                'credits' => $credits,
            ];
        }

        try {
            $userSnapshot->increment('ai_quota_used', $credits);
        } catch (Throwable) {
            return [
                'reserved' => false,
                'bypassed' => false,
                'reservation' => null,
                'user_id' => $userSnapshot->id,
                'credits' => $credits,
            ];
        }

        return [
            'reserved' => true,
            'bypassed' => false,
            'reservation' => null,
            'user_id' => $userSnapshot->id,
            'credits' => $credits,
        ];
    }
}
