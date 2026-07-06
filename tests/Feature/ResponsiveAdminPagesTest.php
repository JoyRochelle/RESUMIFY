<?php

namespace Tests\Feature;

use App\Models\AiUsageLog;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResponsiveAdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    }

    // =========================================================
    // Mobile tab bar present in all admin pages
    // =========================================================

    public function test_admin_dashboard_contains_mobile_tab_bar(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        // Tab bar nav is present in HTML (hidden via md:hidden CSS class)
        $response->assertSee('md:hidden fixed bottom-0', false);
    }

    public function test_admin_mobile_tab_bar_has_five_tabs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee(route('admin.dashboard'), false);
        $response->assertSee(route('admin.users'), false);
        $response->assertSee(route('admin.support'), false);
        $response->assertSee(route('admin.logs'), false);
        $response->assertSee('more_horiz', false);
    }

    public function test_admin_mobile_tab_bar_more_menu_contains_secondary_pages(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee(route('admin.templates.index'), false);
        $response->assertSee(route('admin.monitor'), false);
        $response->assertSee(route('admin.reports'), false);
    }

    public function test_admin_main_content_has_mobile_bottom_padding(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('pb-24 md:pb-10', false);
    }

    // =========================================================
    // Users page — card layout for mobile
    // =========================================================

    public function test_admin_users_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users'))
            ->assertOk();
    }

    public function test_admin_users_has_mobile_card_layout(): void
    {
        User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);

        $response = $this->actingAs($this->admin)->get(route('admin.users'));
        $response->assertOk();
        // Mobile card wrapper is md:hidden
        $response->assertSee('md:hidden divide-y', false);
    }

    public function test_admin_users_has_desktop_table(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users'));
        $response->assertOk();
        // Desktop table is hidden md:block
        $response->assertSee('hidden md:block', false);
    }

    public function test_admin_users_mobile_card_shows_user_data(): void
    {
        $user = User::factory()->create([
            'name'               => 'Jane Doe',
            'role'               => 'basic',
            'email_verified_at'  => now(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.users'));
        $response->assertOk();
        $response->assertSee('Jane Doe');
        $response->assertSee($user->email);
    }

    // =========================================================
    // Logs page — card layouts for AI + Finance
    // =========================================================

    public function test_admin_logs_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.logs'))
            ->assertOk();
    }

    public function test_admin_logs_ai_tab_has_mobile_card_layout(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.logs'));
        $response->assertOk();
        $response->assertSee('md:hidden divide-y divide-primary/5', false);
    }

    public function test_admin_logs_finance_tab_has_mobile_card_layout(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.logs'));
        $response->assertOk();
        // Both AI and Finance tabs each have a mobile card wrapper
        $html = $response->getContent();
        $count = substr_count($html, 'md:hidden divide-y divide-primary/5');
        $this->assertGreaterThanOrEqual(2, $count, 'Both AI and Finance tabs should have mobile card wrappers');
    }

    public function test_admin_logs_desktop_tables_are_wrapped(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.logs'));
        $response->assertOk();
        $response->assertSee('hidden md:block overflow-x-auto', false);
    }

    // =========================================================
    // Support page — card layout for mobile
    // =========================================================

    public function test_admin_support_page_loads(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.support'))
            ->assertOk();
    }

    public function test_admin_support_has_mobile_card_layout(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.support'));
        $response->assertOk();
        $response->assertSee('md:hidden divide-y divide-primary/5', false);
    }

    public function test_admin_support_has_desktop_table(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.support'));
        $response->assertOk();
        $response->assertSee('hidden md:block', false);
    }

    public function test_admin_support_mobile_card_shows_ticket_data(): void
    {
        $user   = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $ticket = SupportTicket::create(['user_id' => $user->id, 'subject' => 'Mobile Card Test', 'status' => 'open']);
        TicketReply::create(['ticket_id' => $ticket->id, 'user_id' => $user->id, 'body' => 'msg']);

        $response = $this->actingAs($this->admin)->get(route('admin.support'));
        $response->assertOk();
        $response->assertSee('Mobile Card Test');
    }

    // =========================================================
    // Other admin pages load without errors
    // =========================================================

    public function test_admin_monitor_loads(): void
    {
        $this->actingAs($this->admin)->get(route('admin.monitor'))->assertOk();
    }

    public function test_admin_reports_loads(): void
    {
        $this->actingAs($this->admin)->get(route('admin.reports'))->assertOk();
    }

    public function test_admin_template_index_loads(): void
    {
        $this->actingAs($this->admin)->get(route('admin.templates.index'))->assertOk();
    }

    public function test_admin_settings_loads(): void
    {
        $this->actingAs($this->admin)->get(route('admin.settings'))->assertOk();
    }

    // =========================================================
    // Non-admin blocked from seeing responsive layout
    // =========================================================

    public function test_basic_user_cannot_access_admin_pages(): void
    {
        $user = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $this->actingAs($user)->get(route('admin.users'))->assertForbidden();
    }
}
