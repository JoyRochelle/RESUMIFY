<?php

namespace Tests\Feature;

use App\Models\AiUsageLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $basicUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin     = User::factory()->create(['role' => 'admin',  'email_verified_at' => now()]);
        $this->basicUser = User::factory()->create(['role' => 'basic',  'email_verified_at' => now()]);
    }

    // =========================================================
    // Access Control
    // =========================================================

    public function test_admin_can_access_reports_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reports'))
            ->assertOk()
            ->assertViewIs('admin.reports');
    }

    public function test_basic_user_cannot_access_reports_page(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.reports'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_reports_page(): void
    {
        $this->get(route('admin.reports'))
            ->assertRedirect(route('login'));
    }

    public function test_basic_user_cannot_export_pdf(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.reports.export.pdf'))
            ->assertForbidden();
    }

    public function test_basic_user_cannot_export_csv(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.reports.export.csv'))
            ->assertForbidden();
    }

    // =========================================================
    // Index — view variables
    // =========================================================

    public function test_index_passes_required_variables_to_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reports'))
            ->assertViewHasAll(['stats', 'from', 'to', 'dailyRevenue']);
    }

    public function test_from_defaults_to_start_of_current_month(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports'));
        $from     = $response->viewData('from');

        $this->assertEquals(now()->startOfMonth()->toDateString(), $from->toDateString());
    }

    public function test_to_defaults_to_today(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports'));
        $to       = $response->viewData('to');

        $this->assertEquals(now()->toDateString(), $to->toDateString());
    }

    public function test_from_and_to_accept_request_parameters(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports', [
            'from' => '2026-01-01',
            'to'   => '2026-01-31',
        ]));

        $this->assertEquals('2026-01-01', $response->viewData('from')->toDateString());
        $this->assertEquals('2026-01-31', $response->viewData('to')->toDateString());
    }

    public function test_swapped_from_to_are_normalised(): void
    {
        // from > to should be swapped silently
        $response = $this->actingAs($this->admin)->get(route('admin.reports', [
            'from' => '2026-05-31',
            'to'   => '2026-05-01',
        ]));

        $from = $response->viewData('from');
        $to   = $response->viewData('to');

        $this->assertTrue($from->lte($to));
    }

    public function test_stats_array_contains_all_required_keys(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports'));
        $stats    = $response->viewData('stats');

        $this->assertArrayHasKey('new_users',           $stats);
        $this->assertArrayHasKey('premium_conversions', $stats);
        $this->assertArrayHasKey('total_ai_calls',      $stats);
        $this->assertArrayHasKey('total_ai_cost',       $stats);
        $this->assertArrayHasKey('total_revenue',       $stats);
    }

    // =========================================================
    // Stats correctness
    // =========================================================

    public function test_new_users_counts_users_created_in_range(): void
    {
        // 2 users created today — within default current-month range
        User::factory()->count(2)->create(['role' => 'basic', 'email_verified_at' => now()]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports', [
            'from' => now()->toDateString(),
            'to'   => now()->toDateString(),
        ]));

        // setUp creates admin + basicUser (today) + 2 new = at least 4
        $this->assertGreaterThanOrEqual(2, $response->viewData('stats')['new_users']);
    }

    public function test_new_users_excludes_users_outside_range(): void
    {
        // User created last month — via DB to control created_at
        \Illuminate\Support\Facades\DB::table('users')->insert([
            'id'                => (string) Str::ulid(),
            'name'              => 'Old User',
            'email'             => 'old@example.com',
            'password'          => bcrypt('password'),
            'role'              => 'basic',
            'email_verified_at' => now(),
            'created_at'        => now()->subMonth(),
            'updated_at'        => now()->subMonth(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports', [
            'from' => now()->toDateString(),
            'to'   => now()->toDateString(),
        ]));

        $stats = $response->viewData('stats');
        // The old user should not be counted
        $this->assertLessThanOrEqual(
            User::whereDate('created_at', today())->count(),
            $stats['new_users']
        );
    }

    public function test_premium_conversions_counts_only_successful_transactions_in_range(): void
    {
        Transaction::create([
            'id'                => Str::ulid(),
            'user_id'           => $this->basicUser->id,
            'midtrans_order_id' => 'ORD-001',
            'amount'            => 49000,
            'payment_method'    => 'gopay',
            'status'            => 'success',
            'paid_at'           => now(),
        ]);

        // Failed — must NOT count
        Transaction::create([
            'id'                => Str::ulid(),
            'user_id'           => $this->basicUser->id,
            'midtrans_order_id' => 'ORD-002',
            'amount'            => 49000,
            'payment_method'    => 'gopay',
            'status'            => 'failed',
            'paid_at'           => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports', [
            'from' => now()->toDateString(),
            'to'   => now()->toDateString(),
        ]));

        $this->assertEquals(1, $response->viewData('stats')['premium_conversions']);
    }

    public function test_total_revenue_sums_only_successful_transactions(): void
    {
        Transaction::create([
            'id'                => Str::ulid(),
            'user_id'           => $this->basicUser->id,
            'midtrans_order_id' => 'ORD-A',
            'amount'            => 79000,
            'payment_method'    => 'bca',
            'status'            => 'success',
            'paid_at'           => now(),
        ]);

        Transaction::create([
            'id'                => Str::ulid(),
            'user_id'           => $this->basicUser->id,
            'midtrans_order_id' => 'ORD-B',
            'amount'            => 79000,
            'payment_method'    => 'bca',
            'status'            => 'pending',
            'paid_at'           => null,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports', [
            'from' => now()->toDateString(),
            'to'   => now()->toDateString(),
        ]));

        $this->assertEquals(79000.0, $response->viewData('stats')['total_revenue']);
    }

    public function test_total_ai_calls_counts_logs_in_range(): void
    {
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 300,
            'cost_usd'    => 0.003,
        ]);
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'bullet_optimize',
            'tokens_used' => 200,
            'cost_usd'    => 0.002,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports', [
            'from' => now()->toDateString(),
            'to'   => now()->toDateString(),
        ]));

        $this->assertEquals(2, $response->viewData('stats')['total_ai_calls']);
    }

    public function test_total_ai_cost_sums_logs_in_range(): void
    {
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 500,
            'cost_usd'    => 0.005,
        ]);

        // Outside range — DB insert for past date
        \Illuminate\Support\Facades\DB::table('ai_usage_logs')->insert([
            'id'          => (string) Str::ulid(),
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 500,
            'cost_usd'    => 0.005,
            'created_at'  => now()->subMonth(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports', [
            'from' => now()->toDateString(),
            'to'   => now()->toDateString(),
        ]));

        $this->assertEqualsWithDelta(0.005, $response->viewData('stats')['total_ai_cost'], 0.0001);
    }

    public function test_stats_are_zero_when_no_data_in_range(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports', [
            'from' => '2020-01-01',
            'to'   => '2020-01-31',
        ]));

        $stats = $response->viewData('stats');
        $this->assertEquals(0, $stats['premium_conversions']);
        $this->assertEquals(0, $stats['total_ai_calls']);
        $this->assertEquals(0.0, $stats['total_revenue']);
    }

    // =========================================================
    // CSV Export
    // =========================================================

    public function test_csv_export_returns_csv_response(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.export.csv'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.csv', $response->headers->get('Content-Disposition'));
    }

    public function test_csv_contains_all_stat_labels(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.export.csv'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('New Users', $content);
        $this->assertStringContainsString('Total Revenue', $content);
        $this->assertStringContainsString('Total Ai Calls', $content);
        $this->assertStringContainsString('Total Ai Cost', $content);
        $this->assertStringContainsString('Premium Conversions', $content);
    }

    public function test_csv_filename_includes_date_range(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export.csv', [
            'from' => '2026-05-01',
            'to'   => '2026-05-31',
        ]));

        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('2026-05-01', $disposition);
        $this->assertStringContainsString('2026-05-31', $disposition);
    }

    // =========================================================
    // PDF Export
    // =========================================================

    public function test_pdf_export_returns_pdf_response(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.reports.export.pdf'));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_pdf_filename_includes_date_range(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.reports.export.pdf', [
            'from' => '2026-05-01',
            'to'   => '2026-05-31',
        ]));

        $disposition = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('2026-05-01', $disposition);
        $this->assertStringContainsString('2026-05-31', $disposition);
    }

    public function test_pdf_export_renders_without_error(): void
    {
        // Verify dompdf renders cleanly with real data
        Transaction::create([
            'id'                => Str::ulid(),
            'user_id'           => $this->basicUser->id,
            'midtrans_order_id' => 'ORD-PDF-1',
            'amount'            => 49000,
            'payment_method'    => 'gopay',
            'status'            => 'success',
            'paid_at'           => now(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.reports.export.pdf', [
                'from' => now()->toDateString(),
                'to'   => now()->toDateString(),
            ]))
            ->assertOk();
    }
}
