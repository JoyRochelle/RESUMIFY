<?php

namespace Tests\Feature;

use App\Models\AiUsageLog;
use App\Models\CvTemplate;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $basicUser;
    private User $premiumUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin       = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $this->basicUser   = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $this->premiumUser = User::factory()->create(['role' => 'premium', 'email_verified_at' => now()]);
    }

    // --- Access control ---

    public function test_admin_can_access_dashboard(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewIs('admin.dashboard');
    }

    public function test_basic_user_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_premium_user_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->premiumUser)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    // --- View variables ---

    public function test_dashboard_passes_all_required_variables_to_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertViewHasAll([
                'totalRevenue',
                'revenueGrowth',
                'aiCostUsd',
                'openTickets',
                'premiumUsers',
                'chartData',
                'sentryErrors',
                'recentUsers',
                'recentTickets',
                'templatePerformance',
                'maxCvCount',
            ]);
    }

    // --- Stats correctness ---

    public function test_total_revenue_counts_only_current_month_successful_transactions(): void
    {
        Transaction::create([
            'id'                     => Str::ulid(),
            'user_id'                => $this->basicUser->id,
            'midtrans_order_id'      => 'ORDER-001',
            'amount'                 => 49000,
            'payment_method'         => 'gopay',
            'status'                 => 'success',
            'paid_at'                => now(),
        ]);

        // Failed transaction — must NOT be counted
        Transaction::create([
            'id'                     => Str::ulid(),
            'user_id'                => $this->basicUser->id,
            'midtrans_order_id'      => 'ORDER-002',
            'amount'                 => 49000,
            'payment_method'         => 'gopay',
            'status'                 => 'failed',
            'paid_at'                => now(),
        ]);

        // Last month success — must NOT be counted
        Transaction::create([
            'id'                     => Str::ulid(),
            'user_id'                => $this->basicUser->id,
            'midtrans_order_id'      => 'ORDER-003',
            'amount'                 => 49000,
            'payment_method'         => 'gopay',
            'status'                 => 'success',
            'paid_at'                => now()->subMonth(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertViewHas('totalRevenue', 49000.00);
    }

    public function test_ai_cost_sums_current_month_only(): void
    {
        // This month — via Eloquent (created_at auto-fills to now())
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 500,
            'cost_usd'    => 0.005000,
        ]);

        // Last month — DB::table() bypasses Eloquent auto-timestamp so created_at is honoured
        DB::table('ai_usage_logs')->insert([
            'id'          => (string) Str::ulid(),
            'user_id'     => $this->basicUser->id,
            'action_type' => 'bullet_optimize',
            'tokens_used' => 300,
            'cost_usd'    => 0.003000,
            'created_at'  => now()->subMonth(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $this->assertEqualsWithDelta(0.005, $response->viewData('aiCostUsd'), 0.0001);
    }

    public function test_open_tickets_count_excludes_closed_and_pending(): void
    {
        SupportTicket::create(['id' => Str::ulid(), 'user_id' => $this->basicUser->id, 'subject' => 'Open ticket',    'status' => 'open']);
        SupportTicket::create(['id' => Str::ulid(), 'user_id' => $this->basicUser->id, 'subject' => 'Pending ticket', 'status' => 'pending']);
        SupportTicket::create(['id' => Str::ulid(), 'user_id' => $this->basicUser->id, 'subject' => 'Closed ticket',  'status' => 'closed']);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertViewHas('openTickets', 1);
    }

    public function test_premium_users_count_excludes_basic_and_admin(): void
    {
        // setUp already created 1 admin, 1 basic, 1 premium
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertViewHas('premiumUsers', 1);
    }

    // --- Chart data ---

    public function test_chart_data_contains_exactly_30_labels(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $chartData = $response->viewData('chartData');

        $this->assertCount(30, $chartData['labels']);
        $this->assertCount(30, $chartData['activeUsers']);
        $this->assertCount(30, $chartData['aiCalls']);
    }

    public function test_chart_data_values_are_integers(): void
    {
        $response  = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $chartData = $response->viewData('chartData');

        foreach ($chartData['activeUsers'] as $value) {
            $this->assertIsInt($value);
        }
        foreach ($chartData['aiCalls'] as $value) {
            $this->assertIsInt($value);
        }
    }

    public function test_chart_data_reflects_users_created_in_last_30_days(): void
    {
        // 2 extra users created today (setUp already created 3)
        User::factory()->count(2)->create(['role' => 'basic', 'email_verified_at' => now()]);

        $response  = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $chartData = $response->viewData('chartData');

        // Last entry = today's count; setUp created 3, plus 2 here = 5 today
        $todayCount = $chartData['activeUsers'][29];
        $this->assertGreaterThanOrEqual(5, $todayCount);
    }

    // --- Supporting data ---

    public function test_recent_users_contains_at_most_5_non_admin_users(): void
    {
        User::factory()->count(8)->create(['role' => 'basic', 'email_verified_at' => now()]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $this->assertLessThanOrEqual(5, $response->viewData('recentUsers')->count());
    }

    public function test_recent_users_does_not_include_admin_accounts(): void
    {
        $response    = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $recentUsers = $response->viewData('recentUsers');

        foreach ($recentUsers as $user) {
            $this->assertNotEquals('admin', $user->role);
        }
    }

    public function test_recent_tickets_contains_at_most_3_open_tickets(): void
    {
        SupportTicket::factory()->count(5)->create([
            'user_id' => $this->basicUser->id,
            'status'  => 'open',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $this->assertLessThanOrEqual(3, $response->viewData('recentTickets')->count());
    }

    public function test_template_performance_contains_at_most_4_active_templates(): void
    {
        CvTemplate::factory()->count(6)->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $this->assertLessThanOrEqual(4, $response->viewData('templatePerformance')->count());
    }

    // --- Sentry fallback ---

    public function test_sentry_errors_is_null_when_credentials_not_configured(): void
    {
        // Default test env has no SENTRY_AUTH_TOKEN
        Cache::forget('sentry_error_count');

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $this->assertNull($response->viewData('sentryErrors'));
    }

    // --- Suspended admin edge case ---

    public function test_suspended_admin_can_still_access_dashboard(): void
    {
        // Admins bypass suspension checks per spec
        $this->admin->forceFill(['is_suspended' => true])->save();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }
}
