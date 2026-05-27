<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavTest extends TestCase
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
    // Route existence — gate check
    // =========================================================

    public function test_admin_dashboard_route_resolves_to_controller(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_monitor_route_resolves_to_controller(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.monitor'))
            ->assertOk();
    }

    public function test_admin_reports_route_resolves_to_controller(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reports'))
            ->assertOk();
    }

    public function test_admin_logs_route_resolves_to_controller(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.logs'))
            ->assertOk();
    }

    public function test_admin_support_route_resolves_to_controller(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.support'))
            ->assertOk();
    }

    public function test_admin_users_route_resolves_to_controller(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users'))
            ->assertOk();
    }

    // =========================================================
    // Sidenav — Monitor and Reports links present
    // =========================================================

    public function test_sidenav_contains_monitor_link(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertSee(route('admin.monitor'));
    }

    public function test_sidenav_contains_reports_link(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertSee(route('admin.reports'));
    }

    public function test_sidenav_contains_monitor_icon(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertSee('monitor_heart');
    }

    public function test_sidenav_contains_reports_icon(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertSee('bar_chart_4_bars');
    }

    public function test_sidenav_shows_monitor_as_active_on_monitor_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.monitor'))
            ->assertSee('System Monitor');
    }

    public function test_sidenav_shows_reports_as_active_on_reports_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.reports'))
            ->assertSee('Revenue Report');
    }

    // =========================================================
    // Help center routes
    // =========================================================

    public function test_help_index_route_resolves(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('user.help'))
            ->assertOk();
    }

    public function test_help_tickets_route_resolves(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('help.tickets'))
            ->assertOk();
    }

    public function test_help_contact_route_is_post(): void
    {
        $this->actingAs($this->basicUser)
            ->post(route('help.contact'), [
                'subject' => 'Route test',
                'message' => 'Verifying contact route exists.',
            ])
            ->assertRedirect(route('user.help'));
    }

    // =========================================================
    // All admin routes require auth + admin role
    // =========================================================

    public function test_basic_user_cannot_access_admin_monitor(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.monitor'))
            ->assertForbidden();
    }

    public function test_basic_user_cannot_access_admin_reports(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.reports'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_admin_monitor(): void
    {
        $this->get(route('admin.monitor'))
            ->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_from_admin_reports(): void
    {
        $this->get(route('admin.reports'))
            ->assertRedirect(route('login'));
    }
}
