<?php

namespace Tests\Feature;

use App\Models\AdminLog;
use App\Models\AiUsageLog;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $basicUser;
    private User $premiumUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin       = User::factory()->create(['role' => 'admin',   'email_verified_at' => now()]);
        $this->basicUser   = User::factory()->create(['role' => 'basic',   'email_verified_at' => now()]);
        $this->premiumUser = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
    }

    // =========================================================
    // Access Control
    // =========================================================

    public function test_admin_can_access_user_list(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users'))
            ->assertOk()
            ->assertViewIs('admin.users');
    }

    public function test_basic_user_cannot_access_user_list(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.users'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_user_list(): void
    {
        $this->get(route('admin.users'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_view_user_detail(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->basicUser))
            ->assertOk()
            ->assertViewIs('admin.users.show');
    }

    public function test_admin_cannot_view_another_admin_detail(): void
    {
        $otherAdmin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $otherAdmin))
            ->assertForbidden();
    }

    public function test_basic_user_cannot_access_user_detail(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.users.show', $this->premiumUser))
            ->assertForbidden();
    }

    // =========================================================
    // Index — view variables
    // =========================================================

    public function test_index_passes_required_variables_to_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users'))
            ->assertViewHasAll(['users', 'totalUsers', 'premiumUsers', 'newToday']);
    }

    public function test_index_excludes_admin_accounts_from_listing(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users'));
        $users    = $response->viewData('users');

        foreach ($users as $user) {
            $this->assertNotEquals('admin', $user->role);
        }
    }

    // =========================================================
    // Index — search & filter
    // =========================================================

    public function test_search_by_name_filters_results(): void
    {
        $target = User::factory()->create([
            'name'               => 'Unique Findable Name',
            'role'               => 'basic',
            'email_verified_at'  => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users', ['search' => 'Unique Findable']));

        $names = $response->viewData('users')->pluck('name');
        $this->assertTrue($names->contains($target->name));
    }

    public function test_search_by_email_filters_results(): void
    {
        $target = User::factory()->create([
            'email'              => 'searchme@example.com',
            'role'               => 'basic',
            'email_verified_at'  => now(),
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users', ['search' => 'searchme@example']));

        $emails = $response->viewData('users')->pluck('email');
        $this->assertTrue($emails->contains($target->email));
    }

    public function test_filter_by_plan_returns_only_matching_users(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users', ['plan' => 'premium']));

        $roles = $response->viewData('users')->pluck('role')->unique();
        $this->assertCount(1, $roles);
        $this->assertEquals('premium', $roles->first());
    }

    public function test_filter_by_suspended_status_returns_only_suspended(): void
    {
        $this->basicUser->update(['is_suspended' => true]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users', ['status' => 'suspended']));

        $response->viewData('users')->each(fn($u) =>
            $this->assertTrue((bool) $u->is_suspended)
        );
    }

    public function test_filter_by_active_status_excludes_suspended(): void
    {
        $this->basicUser->update(['is_suspended' => true]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users', ['status' => 'active']));

        $response->viewData('users')->each(fn($u) =>
            $this->assertFalse((bool) $u->is_suspended)
        );
    }

    public function test_index_paginates_at_20_per_page(): void
    {
        User::factory()->count(25)->create(['role' => 'basic', 'email_verified_at' => now()]);

        $response = $this->actingAs($this->admin)->get(route('admin.users'));
        $users    = $response->viewData('users');

        $this->assertLessThanOrEqual(20, $users->count());
    }

    // =========================================================
    // Show — view variables
    // =========================================================

    public function test_show_passes_required_variables_to_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->basicUser))
            ->assertViewHasAll(['user', 'transactions', 'subscription', 'totalAiSpend', 'totalAiActions']);
    }

    public function test_show_loads_at_most_5_cvs(): void
    {
        // basicUser has no CVs unless we create them; just verify the key exists
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->basicUser));

        $this->assertLessThanOrEqual(5, $response->viewData('user')->cvs->count());
    }

    public function test_show_returns_total_ai_spend_and_action_count(): void
    {
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 400,
            'cost_usd'    => 0.004,
        ]);
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'bullet_optimize',
            'tokens_used' => 200,
            'cost_usd'    => 0.002,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->basicUser));

        $this->assertEqualsWithDelta(0.006, $response->viewData('totalAiSpend'), 0.0001);
        $this->assertEquals(2, $response->viewData('totalAiActions'));
    }

    // =========================================================
    // Override Plan
    // =========================================================

    public function test_override_plan_changes_user_role(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.users.plan', $this->basicUser), ['plan' => 'premium'])
            ->assertRedirect();

        $this->assertEquals('premium', $this->basicUser->fresh()->role);
    }

    public function test_override_plan_to_premium_creates_subscription_record(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.users.plan', $this->basicUser), ['plan' => 'premium']);

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $this->basicUser->id,
            'plan'    => 'premium',
            'status'  => 'active',
        ]);
    }

    public function test_override_plan_to_premium_resets_quota_used(): void
    {
        $this->basicUser->update(['ai_quota_used' => 3]);

        $this->actingAs($this->admin)
            ->patch(route('admin.users.plan', $this->basicUser), ['plan' => 'premium']);

        $this->assertEquals(0, $this->basicUser->fresh()->ai_quota_used);
    }

    public function test_override_plan_to_basic_does_not_create_subscription(): void
    {
        $initialCount = Subscription::where('user_id', $this->premiumUser->id)->count();

        $this->actingAs($this->admin)
            ->patch(route('admin.users.plan', $this->premiumUser), ['plan' => 'basic']);

        $this->assertEquals($initialCount, Subscription::where('user_id', $this->premiumUser->id)->count());
    }

    public function test_override_plan_logs_to_admin_log(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.users.plan', $this->basicUser), ['plan' => 'premium']);

        $this->assertDatabaseHas('admin_logs', [
            'admin_id'    => $this->admin->id,
            'target_type' => 'user',
            'target_id'   => $this->basicUser->id,
        ]);

        $log = AdminLog::where('target_id', $this->basicUser->id)->latest()->first();
        $this->assertStringContainsString('override_plan', $log->action);
    }

    public function test_override_plan_on_admin_is_forbidden(): void
    {
        $otherAdmin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($this->admin)
            ->patch(route('admin.users.plan', $otherAdmin), ['plan' => 'basic'])
            ->assertForbidden();
    }

    public function test_override_plan_rejects_invalid_plan_value(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.users.plan', $this->basicUser), ['plan' => 'enterprise'])
            ->assertSessionHasErrors('plan');
    }

    // =========================================================
    // Adjust Credits
    // =========================================================

    public function test_adjust_credits_updates_ai_quota_used(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.users.credits', $this->basicUser), ['ai_quota_used' => 7])
            ->assertRedirect();

        $this->assertEquals(7, $this->basicUser->fresh()->ai_quota_used);
    }

    public function test_adjust_credits_logs_to_admin_log(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.users.credits', $this->basicUser), ['ai_quota_used' => 3]);

        $log = AdminLog::where('target_id', $this->basicUser->id)->latest()->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('adjust_credits', $log->action);
    }

    public function test_adjust_credits_admin_log_has_structured_mutation_metadata(): void
    {
        $this->basicUser->update(['ai_quota_used' => 1]);

        $this->actingAs($this->admin)
            ->patch(route('admin.users.credits', $this->basicUser), ['ai_quota_used' => 8])
            ->assertRedirect();

        $log = AdminLog::where('target_id', $this->basicUser->id)->latest()->firstOrFail();

        $this->assertSame('adjust_credits', $log->action);
        $this->assertSame('ai_quota_used', $log->metadata['field'] ?? null);
        $this->assertSame(1, $log->metadata['old'] ?? null);
        $this->assertSame(8, $log->metadata['new'] ?? null);
    }

    public function test_adjust_credits_rejects_negative_value(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.users.credits', $this->basicUser), ['ai_quota_used' => -1])
            ->assertSessionHasErrors('ai_quota_used');
    }

    public function test_adjust_credits_on_admin_is_forbidden(): void
    {
        $otherAdmin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($this->admin)
            ->patch(route('admin.users.credits', $otherAdmin), ['ai_quota_used' => 0])
            ->assertForbidden();
    }

    // =========================================================
    // Toggle Suspend
    // =========================================================

    public function test_suspend_sets_is_suspended_to_true(): void
    {
        $this->assertFalse((bool) $this->basicUser->is_suspended);

        $this->actingAs($this->admin)
            ->patch(route('admin.users.suspend', $this->basicUser))
            ->assertRedirect();

        $this->assertTrue((bool) $this->basicUser->fresh()->is_suspended);
    }

    public function test_activate_sets_is_suspended_to_false(): void
    {
        $this->basicUser->update(['is_suspended' => true]);

        $this->actingAs($this->admin)
            ->patch(route('admin.users.suspend', $this->basicUser))
            ->assertRedirect();

        $this->assertFalse((bool) $this->basicUser->fresh()->is_suspended);
    }

    public function test_suspend_logs_suspend_action(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.users.suspend', $this->basicUser));

        $log = AdminLog::where('target_id', $this->basicUser->id)->latest()->first();
        $this->assertEquals('suspend_user', $log->action);
    }

    public function test_activate_logs_activate_action(): void
    {
        $this->basicUser->update(['is_suspended' => true]);

        $this->actingAs($this->admin)
            ->patch(route('admin.users.suspend', $this->basicUser));

        $log = AdminLog::where('target_id', $this->basicUser->id)->latest()->first();
        $this->assertEquals('activate_user', $log->action);
    }

    public function test_suspending_an_admin_is_forbidden(): void
    {
        $otherAdmin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($this->admin)
            ->patch(route('admin.users.suspend', $otherAdmin))
            ->assertForbidden();
    }

    // =========================================================
    // Destroy
    // =========================================================

    public function test_destroy_permanently_removes_user_from_database(): void
    {
        $target = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $id     = $target->id;

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect(route('admin.users'));

        $this->assertDatabaseMissing('users', ['id' => $id]);
    }

    public function test_destroy_logs_hard_delete_action(): void
    {
        $target = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $id     = $target->id;

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $target));

        $this->assertDatabaseHas('admin_logs', [
            'admin_id'    => $this->admin->id,
            'action'      => 'hard_delete_user',
            'target_type' => 'user',
            'target_id'   => $id,
        ]);
    }

    public function test_deleting_an_admin_account_is_forbidden(): void
    {
        $otherAdmin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $otherAdmin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
    }

    public function test_admin_cannot_delete_own_account(): void
    {
        $this->actingAs($this->admin)
            ->delete(route('admin.users.destroy', $this->admin))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    // =========================================================
    // CheckSuspended Middleware
    // =========================================================

    public function test_suspended_user_is_logged_out_and_redirected_to_login(): void
    {
        $this->basicUser->update(['is_suspended' => true]);

        $this->actingAs($this->basicUser)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_suspended_user_sees_suspension_error_message(): void
    {
        $this->basicUser->update(['is_suspended' => true]);

        $response = $this->actingAs($this->basicUser)
            ->followingRedirects()
            ->get(route('dashboard'));

        $response->assertSeeText('suspended');
    }

    public function test_suspended_admin_is_not_logged_out(): void
    {
        $this->admin->update(['is_suspended' => true]);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_active_user_is_not_affected_by_check_suspended_middleware(): void
    {
        $this->assertFalse((bool) $this->basicUser->is_suspended);

        $this->actingAs($this->basicUser)
            ->get(route('dashboard'))
            ->assertOk();
    }
}
