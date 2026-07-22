<?php

namespace Tests\Feature;

use App\Models\Cv;
use App\Models\CvTemplate;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResponsiveUserPagesTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
    }

    private function createCvForUser(): Cv
    {
        $template = CvTemplate::factory()->create(['is_active' => true]);
        return Cv::forceCreate([
            'id'          => (string) Str::ulid(),
            'user_id'     => $this->user->id,
            'template_id' => $template->id,
            'title'       => 'Test Resume',
            'status'      => 'draft',
        ]);
    }

    // =========================================================
    // Auth pages — responsive grid fix
    // =========================================================

    public function test_login_page_loads_ok(): void
    {
        $this->get(route('login'))->assertOk();
    }

    public function test_register_page_password_grid_uses_responsive_cols(): void
    {
        $response = $this->get(route('register'));
        $response->assertOk();
        // Password/confirm grid must stack on mobile (grid-cols-1) and expand on sm
        $response->assertSee('grid-cols-1 sm:grid-cols-2', false);
    }

    // =========================================================
    // Dashboard — padding & heading responsive
    // =========================================================

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_dashboard_heading_uses_responsive_text_size(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('text-3xl sm:text-4xl md:text-6xl', false);
    }

    public function test_dashboard_create_modal_uses_max_height_not_fixed(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('max-h-[90vh]', false);
        $response->assertDontSee('h-[85vh]', false);
    }

    public function test_dashboard_has_mobile_bottom_padding(): void
    {
        $response = $this->actingAs($this->user)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('pb-24', false);
    }

    // =========================================================
    // Manuscript — mobile tab bar
    // =========================================================

    public function test_manuscript_page_loads_for_authenticated_user(): void
    {
        $this->createCvForUser();
        $this->actingAs($this->user)
            ->get(route('user.manuscript'))
            ->assertOk();
    }

    public function test_manuscript_has_mobile_edit_tab_button(): void
    {
        $cv = $this->createCvForUser();
        $response = $this->actingAs($this->user)->get(route('user.manuscript', ['cv_id' => $cv->id]));
        $response->assertOk();
        $response->assertSee('ms-tab-edit', false);
    }

    public function test_manuscript_has_mobile_preview_tab_button(): void
    {
        $cv = $this->createCvForUser();
        $response = $this->actingAs($this->user)->get(route('user.manuscript', ['cv_id' => $cv->id]));
        $response->assertOk();
        $response->assertSee('ms-tab-preview', false);
    }

    public function test_manuscript_tab_bar_is_hidden_on_large_screens(): void
    {
        $cv = $this->createCvForUser();
        $response = $this->actingAs($this->user)->get(route('user.manuscript', ['cv_id' => $cv->id]));
        $response->assertOk();
        $response->assertSee('flex lg:hidden', false);
    }

    public function test_manuscript_preview_panel_hidden_on_mobile_initially(): void
    {
        $cv = $this->createCvForUser();
        $response = $this->actingAs($this->user)->get(route('user.manuscript', ['cv_id' => $cv->id]));
        $response->assertOk();
        $response->assertSee('ms-panel-preview', false);
        $response->assertSee('hidden lg:flex', false);
    }

    public function test_manuscript_has_switchMsTab_js_function(): void
    {
        $cv = $this->createCvForUser();
        $response = $this->actingAs($this->user)->get(route('user.manuscript', ['cv_id' => $cv->id]));
        $response->assertOk();
        $response->assertSee('switchMsTab', false);
    }

    // =========================================================
    // ATS Analyzer — mobile tab bar
    // =========================================================

    public function test_ats_analyzer_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('user.ai-assistant'))
            ->assertOk();
    }

    public function test_ats_analyzer_has_mobile_setup_tab_button(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.ai-assistant'));
        $response->assertOk();
        $response->assertSee('ats-tab-setup', false);
    }

    public function test_ats_analyzer_has_mobile_results_tab_button(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.ai-assistant'));
        $response->assertOk();
        $response->assertSee('ats-tab-results', false);
    }

    public function test_ats_analyzer_results_panel_hidden_on_mobile_initially(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.ai-assistant'));
        $response->assertOk();
        $response->assertSee('ats-panel-results', false);
        $response->assertSee('hidden lg:block', false);
    }

    public function test_ats_analyzer_has_switchAtsTab_js_function(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.ai-assistant'));
        $response->assertOk();
        $response->assertSee('switchAtsTab', false);
    }

    // =========================================================
    // Upgrade Quota — bottom padding + grid breakpoints
    // =========================================================

    public function test_upgrade_quota_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('user.upgrade-quota'))
            ->assertOk();
    }

    public function test_upgrade_quota_has_mobile_bottom_padding(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.upgrade-quota'));
        $response->assertOk();
        $response->assertSee('pb-20 md:pb-0', false);
    }

    public function test_upgrade_quota_pricing_grid_uses_md_breakpoint(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.upgrade-quota'));
        $response->assertOk();
        $response->assertSee('md:grid-cols-2', false);
    }

    // =========================================================
    // Help Center — padding & text size
    // =========================================================

    public function test_help_page_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('user.help'))
            ->assertOk();
    }

    public function test_help_page_has_mobile_bottom_padding(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.help'));
        $response->assertOk();
        $response->assertSee('pb-20 md:pb-0', false);
    }

    public function test_help_heading_uses_responsive_text_size(): void
    {
        $response = $this->actingAs($this->user)->get(route('user.help'));
        $response->assertOk();
        $response->assertSee('text-3xl md:text-5xl', false);
    }

    public function test_settings_page_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('user.settings'))
            ->assertOk();
    }

    // =========================================================
    // Tickets list + show — padding
    // =========================================================

    public function test_tickets_list_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('help.tickets'))
            ->assertOk();
    }

    public function test_tickets_list_uses_responsive_padding(): void
    {
        $response = $this->actingAs($this->user)->get(route('help.tickets'));
        $response->assertOk();
        $response->assertSee('max-w-5xl', false);
        $response->assertSee('px-4 sm:px-6 md:px-12', false);
    }

    public function test_ticket_show_loads_for_ticket_owner(): void
    {
        $ticket = SupportTicket::create([
            'user_id' => $this->user->id,
            'subject' => 'Test ticket',
            'status'  => 'open',
        ]);
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $this->user->id,
            'body'      => 'Initial message',
        ]);

        $this->actingAs($this->user)
            ->get(route('help.tickets.show', $ticket))
            ->assertOk();
    }

    public function test_ticket_show_uses_responsive_padding(): void
    {
        $ticket = SupportTicket::create([
            'user_id' => $this->user->id,
            'subject' => 'Test ticket',
            'status'  => 'open',
        ]);
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $this->user->id,
            'body'      => 'Initial message',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('help.tickets.show', $ticket));

        $response->assertOk();
        $response->assertSee('px-4 sm:px-6 md:px-12', false);
    }
}
