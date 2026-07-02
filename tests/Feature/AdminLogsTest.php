<?php

namespace Tests\Feature;

use App\Models\AiUsageLog;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminLogsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $basicUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin     = User::factory()->create(['role' => 'admin',   'email_verified_at' => now()]);
        $this->basicUser = User::factory()->create(['role' => 'basic',   'email_verified_at' => now()]);
    }

    // =========================================================
    // Access Control
    // =========================================================

    public function test_admin_can_access_logs_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.logs'))
            ->assertOk()
            ->assertViewIs('admin.logs');
    }

    public function test_basic_user_cannot_access_logs_page(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.logs'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_logs_page(): void
    {
        $this->get(route('admin.logs'))
            ->assertRedirect(route('login'));
    }

    public function test_basic_user_cannot_export_ai_csv(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.logs.export.ai'))
            ->assertForbidden();
    }

    public function test_basic_user_cannot_export_finance_csv(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.logs.export.finance'))
            ->assertForbidden();
    }

    // =========================================================
    // Index — view variables
    // =========================================================

    public function test_index_passes_required_variables_to_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.logs'))
            ->assertViewHasAll([
                'tab', 'from', 'to',
                'aiLogs', 'transactions',
                'totalAiCostMtd', 'totalRevenueMtd',
                'totalAiActions', 'totalTxCount',
            ]);
    }

    public function test_default_tab_is_ai(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.logs'));
        $this->assertEquals('ai', $response->viewData('tab'));
    }

    public function test_tab_parameter_is_passed_to_view(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.logs', ['tab' => 'finance']));

        $this->assertEquals('finance', $response->viewData('tab'));
    }

    // =========================================================
    // Stats correctness
    // =========================================================

    public function test_total_ai_cost_mtd_sums_only_current_month(): void
    {
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 500,
            'cost_usd'    => 0.005,
        ]);

        // Last month — insert via DB to bypass Eloquent timestamps
        \Illuminate\Support\Facades\DB::table('ai_usage_logs')->insert([
            'id'          => (string) Str::ulid(),
            'user_id'     => $this->basicUser->id,
            'action_type' => 'bullet_optimize',
            'tokens_used' => 300,
            'cost_usd'    => 0.003,
            'created_at'  => now()->subMonth(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs'));

        $this->assertEqualsWithDelta(0.005, $response->viewData('totalAiCostMtd'), 0.0001);
    }

    public function test_total_revenue_mtd_counts_only_successful_transactions_this_month(): void
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

        // Failed — must not count
        Transaction::create([
            'id'                => Str::ulid(),
            'user_id'           => $this->basicUser->id,
            'midtrans_order_id' => 'ORD-002',
            'amount'            => 49000,
            'payment_method'    => 'gopay',
            'status'            => 'failed',
            'paid_at'           => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs'));

        $this->assertEquals(49000.00, (float) $response->viewData('totalRevenueMtd'));
    }

    public function test_total_ai_actions_counts_all_time(): void
    {
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 100,
            'cost_usd'    => 0.001,
        ]);
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'bullet_optimize',
            'tokens_used' => 200,
            'cost_usd'    => 0.002,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs'));

        $this->assertEquals(2, $response->viewData('totalAiActions'));
    }

    public function test_total_tx_count_counts_all_statuses(): void
    {
        foreach (['success', 'pending', 'failed', 'expired'] as $i => $status) {
            Transaction::create([
                'id'                => Str::ulid(),
                'user_id'           => $this->basicUser->id,
                'midtrans_order_id' => "ORD-00{$i}",
                'amount'            => 49000,
                'payment_method'    => 'gopay',
                'status'            => $status,
                'paid_at'           => $status === 'success' ? now() : null,
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.logs'));

        $this->assertEquals(4, $response->viewData('totalTxCount'));
    }

    // =========================================================
    // AI Logs listing & date filter
    // =========================================================

    public function test_ai_logs_shows_real_db_rows(): void
    {
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'bullet_optimize',
            'tokens_used' => 400,
            'cost_usd'    => 0.004,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs'));
        $aiLogs   = $response->viewData('aiLogs');

        $this->assertGreaterThanOrEqual(1, $aiLogs->total());
    }

    public function test_ai_logs_date_from_filter_excludes_older_records(): void
    {
        // Old record inserted via DB to set created_at in the past
        \Illuminate\Support\Facades\DB::table('ai_usage_logs')->insert([
            'id'          => (string) Str::ulid(),
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 100,
            'cost_usd'    => 0.001,
            'created_at'  => now()->subDays(10),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs', [
            'from' => now()->subDays(2)->toDateString(),
        ]));

        $this->assertEquals(0, $response->viewData('aiLogs')->total());
    }

    public function test_ai_logs_date_to_filter_excludes_newer_records(): void
    {
        // Record created today
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 100,
            'cost_usd'    => 0.001,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs', [
            'to' => now()->subDays(5)->toDateString(),
        ]));

        $this->assertEquals(0, $response->viewData('aiLogs')->total());
    }

    public function test_ai_logs_paginate_at_50_per_page(): void
    {
        for ($i = 0; $i < 55; $i++) {
            AiUsageLog::create([
                'user_id'     => $this->basicUser->id,
                'action_type' => 'ats_analyze',
                'tokens_used' => 100,
                'cost_usd'    => 0.001,
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.logs'));
        $this->assertLessThanOrEqual(50, $response->viewData('aiLogs')->count());
    }

    // =========================================================
    // Finance Logs listing & date filter
    // =========================================================

    public function test_finance_logs_shows_all_four_statuses(): void
    {
        foreach (['success', 'pending', 'failed', 'expired'] as $i => $status) {
            Transaction::create([
                'id'                => Str::ulid(),
                'user_id'           => $this->basicUser->id,
                'midtrans_order_id' => "ORD-{$i}",
                'amount'            => 49000,
                'payment_method'    => 'gopay',
                'status'            => $status,
                'paid_at'           => $status === 'success' ? now() : null,
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.logs', ['tab' => 'finance']));

        $statuses = $response->viewData('transactions')->pluck('status')->unique()->sort()->values();
        $this->assertCount(4, $statuses);
    }

    public function test_finance_date_filter_works(): void
    {
        \Illuminate\Support\Facades\DB::table('transactions')->insert([
            'id'                => (string) Str::ulid(),
            'user_id'           => $this->basicUser->id,
            'midtrans_order_id' => 'OLD-001',
            'amount'            => 49000,
            'payment_method'    => 'gopay',
            'status'            => 'success',
            'paid_at'           => now()->subDays(20),
            'created_at'        => now()->subDays(20),
            'updated_at'        => now()->subDays(20),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs', [
            'tab'  => 'finance',
            'from' => now()->subDays(5)->toDateString(),
        ]));

        $this->assertEquals(0, $response->viewData('transactions')->total());
    }

    public function test_finance_logs_paginate_at_50_per_page(): void
    {
        for ($i = 0; $i < 55; $i++) {
            Transaction::create([
                'id'                => Str::ulid(),
                'user_id'           => $this->basicUser->id,
                'midtrans_order_id' => "ORD-BULK-{$i}",
                'amount'            => 49000,
                'payment_method'    => 'gopay',
                'status'            => 'pending',
                'paid_at'           => null,
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.logs', ['tab' => 'finance']));

        $this->assertLessThanOrEqual(50, $response->viewData('transactions')->count());
    }

    // =========================================================
    // CSV Exports
    // =========================================================

    public function test_ai_csv_export_returns_csv_response(): void
    {
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 500,
            'cost_usd'    => 0.005,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.logs.export.ai'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.csv', $response->headers->get('Content-Disposition'));
    }

    public function test_ai_csv_contains_header_row(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.logs.export.ai'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Action Type', $content);
        $this->assertStringContainsString('Tokens Used', $content);
        $this->assertStringContainsString('Cost USD', $content);
    }

    public function test_ai_csv_contains_data_rows(): void
    {
        AiUsageLog::create([
            'user_id'     => $this->basicUser->id,
            'action_type' => 'bullet_optimize',
            'tokens_used' => 300,
            'cost_usd'    => 0.003,
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.logs.export.ai'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('bullet_optimize', $content);
        $this->assertStringContainsString($this->basicUser->email, $content);
    }

    public function test_finance_csv_export_returns_csv_response(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.logs.export.finance'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment', $response->headers->get('Content-Disposition'));
    }

    public function test_finance_csv_contains_header_row(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.logs.export.finance'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Order ID', $content);
        $this->assertStringContainsString('Amount', $content);
        $this->assertStringContainsString('Status', $content);
    }

    public function test_finance_csv_contains_all_status_types(): void
    {
        foreach (['success', 'pending', 'failed', 'expired'] as $i => $status) {
            Transaction::create([
                'id'                => Str::ulid(),
                'user_id'           => $this->basicUser->id,
                'midtrans_order_id' => "ORD-CSV-{$i}",
                'amount'            => 49000,
                'payment_method'    => 'gopay',
                'status'            => $status,
                'paid_at'           => $status === 'success' ? now() : null,
            ]);
        }

        $response = $this->actingAs($this->admin)
            ->get(route('admin.logs.export.finance'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('success', $content);
        $this->assertStringContainsString('pending', $content);
        $this->assertStringContainsString('failed', $content);
        $this->assertStringContainsString('expired', $content);
    }

    public function test_ai_csv_date_filter_is_applied(): void
    {
        // Old record (30 days ago — outside the from filter)
        \Illuminate\Support\Facades\DB::table('ai_usage_logs')->insert([
            'id'          => (string) Str::ulid(),
            'user_id'     => $this->basicUser->id,
            'action_type' => 'ats_analyze',
            'tokens_used' => 100,
            'cost_usd'    => 0.001,
            'created_at'  => now()->subDays(30),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.logs.export.ai', [
            'from' => now()->subDays(5)->toDateString(),
        ]));

        $content = $response->streamedContent();
        // Only the header line should be present (no data row)
        $lines = array_filter(explode("\n", trim($content)));
        $this->assertCount(1, $lines); // header only
    }

    public function test_ai_csv_export_does_not_lazy_load_user_for_each_row(): void
    {
        for ($i = 0; $i < 40; $i++) {
            $user = User::factory()->create([
                'role' => 'basic',
                'email_verified_at' => now(),
            ]);

            AiUsageLog::create([
                'user_id'     => $user->id,
                'action_type' => 'ats_analyze',
                'tokens_used' => 100 + $i,
                'cost_usd'    => 0.001,
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.logs.export.ai'));

        DB::flushQueryLog();
        DB::enableQueryLog();

        $content = $response->streamedContent();

        $userQueryCount = collect(DB::getQueryLog())
            ->filter(fn (array $query) => str_contains($query['query'], 'from `users`'))
            ->count();

        $this->assertStringContainsString('Tokens Used', $content);
        $this->assertLessThanOrEqual(
            2,
            $userQueryCount,
            'AI CSV export should eager-load users in bounded queries instead of one user query per exported row.'
        );
    }

    public function test_finance_csv_export_does_not_lazy_load_user_for_each_row(): void
    {
        for ($i = 0; $i < 40; $i++) {
            $user = User::factory()->create([
                'role' => 'basic',
                'email_verified_at' => now(),
            ]);

            Transaction::create([
                'user_id'           => $user->id,
                'midtrans_order_id' => "ORD-N1-{$i}",
                'amount'            => 49000 + $i,
                'payment_method'    => 'gopay',
                'status'            => 'success',
                'paid_at'           => now(),
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.logs.export.finance'));

        DB::flushQueryLog();
        DB::enableQueryLog();

        $content = $response->streamedContent();

        $userQueryCount = collect(DB::getQueryLog())
            ->filter(fn (array $query) => str_contains($query['query'], 'from `users`'))
            ->count();

        $this->assertStringContainsString('Order ID', $content);
        $this->assertLessThanOrEqual(
            2,
            $userQueryCount,
            'Finance CSV export should eager-load users in bounded queries instead of one user query per exported row.'
        );
    }
}
