<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminMonitorTest extends TestCase
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

    public function test_admin_can_access_monitor_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.monitor'))
            ->assertOk()
            ->assertViewIs('admin.monitor');
    }

    public function test_basic_user_cannot_access_monitor_page(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.monitor'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_monitor_page(): void
    {
        $this->get(route('admin.monitor'))
            ->assertRedirect(route('login'));
    }

    // =========================================================
    // View variables
    // =========================================================

    public function test_monitor_passes_all_required_variables_to_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.monitor'))
            ->assertViewHasAll([
                'pendingJobs',
                'failedJobs',
                'lastJobAt',
                'diskFree',
                'diskTotal',
                'diskUsed',
                'diskPct',
                'sentryErrors',
                'recentFailed',
            ]);
    }

    // =========================================================
    // Queue health — pending jobs
    // =========================================================

    public function test_pending_jobs_count_reflects_jobs_table(): void
    {
        // Insert 3 fake pending jobs
        DB::table('jobs')->insert([
            ['queue' => 'default', 'payload' => '{}', 'attempts' => 0, 'available_at' => now()->timestamp, 'created_at' => now()->timestamp],
            ['queue' => 'default', 'payload' => '{}', 'attempts' => 0, 'available_at' => now()->timestamp, 'created_at' => now()->timestamp],
            ['queue' => 'default', 'payload' => '{}', 'attempts' => 0, 'available_at' => now()->timestamp, 'created_at' => now()->timestamp],
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertEquals(3, $response->viewData('pendingJobs'));
    }

    public function test_pending_jobs_is_zero_when_table_is_empty(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertEquals(0, $response->viewData('pendingJobs'));
    }

    public function test_last_job_at_is_null_when_no_jobs_queued(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertNull($response->viewData('lastJobAt'));
    }

    public function test_last_job_at_is_carbon_when_jobs_exist(): void
    {
        DB::table('jobs')->insert([
            'queue'        => 'default',
            'payload'      => '{}',
            'attempts'     => 0,
            'available_at' => now()->timestamp,
            'created_at'   => now()->timestamp,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertInstanceOf(\Carbon\Carbon::class, $response->viewData('lastJobAt'));
    }

    // =========================================================
    // Queue health — failed jobs
    // =========================================================

    public function test_failed_jobs_count_reflects_failed_jobs_table(): void
    {
        DB::table('failed_jobs')->insert([
            ['uuid' => 'uuid-1', 'connection' => 'sync', 'queue' => 'default', 'payload' => '{}', 'exception' => 'Error 1', 'failed_at' => now()],
            ['uuid' => 'uuid-2', 'connection' => 'sync', 'queue' => 'default', 'payload' => '{}', 'exception' => 'Error 2', 'failed_at' => now()],
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertEquals(2, $response->viewData('failedJobs'));
    }

    public function test_failed_jobs_is_zero_when_table_is_empty(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertEquals(0, $response->viewData('failedJobs'));
    }

    public function test_recent_failed_contains_at_most_10_entries(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            DB::table('failed_jobs')->insert([
                'uuid'       => "uuid-{$i}",
                'connection' => 'sync',
                'queue'      => 'default',
                'payload'    => '{}',
                'exception'  => "Exception {$i}",
                'failed_at'  => now()->subMinutes($i),
            ]);
        }

        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertLessThanOrEqual(10, $response->viewData('recentFailed')->count());
    }

    public function test_recent_failed_is_empty_collection_when_no_failures(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertCount(0, $response->viewData('recentFailed'));
    }

    // =========================================================
    // Disk usage
    // =========================================================

    public function test_disk_total_is_non_negative(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertGreaterThanOrEqual(0, $response->viewData('diskTotal'));
    }

    public function test_disk_free_is_non_negative(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertGreaterThanOrEqual(0, $response->viewData('diskFree'));
    }

    public function test_disk_pct_is_between_0_and_100(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));
        $pct      = $response->viewData('diskPct');

        $this->assertGreaterThanOrEqual(0, $pct);
        $this->assertLessThanOrEqual(100, $pct);
    }

    public function test_disk_used_equals_total_minus_free(): void
    {
        $response  = $this->actingAs($this->admin)->get(route('admin.monitor'));
        $diskTotal = $response->viewData('diskTotal');
        $diskFree  = $response->viewData('diskFree');
        $diskUsed  = $response->viewData('diskUsed');

        $this->assertEquals($diskTotal - $diskFree, $diskUsed);
    }

    // =========================================================
    // Sentry
    // =========================================================

    public function test_sentry_errors_is_null_when_credentials_not_configured(): void
    {
        // phpunit.xml blanks SENTRY_AUTH_TOKEN so credentials are always absent in tests
        Cache::forget('sentry_error_count');

        $response = $this->actingAs($this->admin)->get(route('admin.monitor'));

        $this->assertNull($response->viewData('sentryErrors'));
    }

    // =========================================================
    // Never 500 — page renders even with empty data
    // =========================================================

    public function test_monitor_renders_ok_with_all_empty_data(): void
    {
        // Ensure all tables are empty (RefreshDatabase handles this)
        Cache::forget('sentry_error_count');

        $this->actingAs($this->admin)
            ->get(route('admin.monitor'))
            ->assertOk();
    }
}
