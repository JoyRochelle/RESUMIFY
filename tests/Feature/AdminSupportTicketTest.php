<?php

namespace Tests\Feature;

use App\Jobs\SendTicketReplyJob;
use App\Models\AdminLog;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class AdminSupportTicketTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $basicUser;
    private SupportTicket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin     = User::factory()->create(['role' => 'admin',  'email_verified_at' => now()]);
        $this->basicUser = User::factory()->create(['role' => 'basic',  'email_verified_at' => now()]);

        $this->ticket = SupportTicket::factory()->create([
            'user_id' => $this->basicUser->id,
            'subject' => 'Cannot export PDF',
            'status'  => 'open',
        ]);
    }

    // =========================================================
    // Access Control — index
    // =========================================================

    public function test_admin_can_access_support_index(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.support'))
            ->assertOk()
            ->assertViewIs('admin.support');
    }

    public function test_basic_user_cannot_access_support_index(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.support'))
            ->assertForbidden();
    }

    public function test_guest_is_redirected_from_support_index(): void
    {
        $this->get(route('admin.support'))
            ->assertRedirect(route('login'));
    }

    // =========================================================
    // Index — view variables
    // =========================================================

    public function test_index_passes_required_variables(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.support'))
            ->assertViewHasAll(['tickets', 'openCount', 'pendingCount', 'closedCount', 'status', 'search', 'admins']);
    }

    public function test_open_count_reflects_open_tickets(): void
    {
        SupportTicket::factory()->create(['user_id' => $this->basicUser->id, 'status' => 'open']);

        $response = $this->actingAs($this->admin)->get(route('admin.support'));

        // setUp creates 1 open ticket, we added 1 more = 2
        $this->assertEquals(2, $response->viewData('openCount'));
    }

    public function test_status_filter_returns_matching_tickets(): void
    {
        SupportTicket::factory()->create(['user_id' => $this->basicUser->id, 'status' => 'closed']);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.support', ['status' => 'closed']));

        $response->viewData('tickets')->each(fn($t) =>
            $this->assertEquals('closed', $t->status)
        );
    }

    public function test_search_filters_by_subject(): void
    {
        SupportTicket::factory()->create([
            'user_id' => $this->basicUser->id,
            'subject' => 'Unique searchable subject XYZ',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.support', ['search' => 'XYZ']));

        $subjects = $response->viewData('tickets')->pluck('subject');
        $this->assertTrue($subjects->contains('Unique searchable subject XYZ'));
    }

    public function test_search_respects_status_filter_when_subject_matches(): void
    {
        $pendingTicket = SupportTicket::factory()->pending()->create([
            'user_id' => $this->basicUser->id,
            'subject' => 'Needle billing question',
        ]);

        $openTicket = SupportTicket::factory()->create([
            'user_id' => $this->basicUser->id,
            'subject' => 'Needle export question',
            'status'  => 'open',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.support', [
                'status' => 'pending',
                'search' => 'Needle',
            ]));

        $tickets = $response->viewData('tickets');

        $this->assertTrue($tickets->pluck('id')->contains($pendingTicket->id));
        $this->assertFalse($tickets->pluck('id')->contains($openTicket->id));
        $tickets->each(fn($ticket) => $this->assertSame('pending', $ticket->status));
    }

    public function test_index_paginates_at_20(): void
    {
        SupportTicket::factory()->count(25)->create(['user_id' => $this->basicUser->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.support'));

        $this->assertLessThanOrEqual(20, $response->viewData('tickets')->count());
    }

    // =========================================================
    // Show
    // =========================================================

    public function test_admin_can_view_ticket_show_page(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.support.show', $this->ticket))
            ->assertOk()
            ->assertViewIs('admin.support.show');
    }

    public function test_show_passes_ticket_and_admins_to_view(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.support.show', $this->ticket))
            ->assertViewHasAll(['ticket', 'admins']);
    }

    public function test_basic_user_cannot_view_ticket_show(): void
    {
        $this->actingAs($this->basicUser)
            ->get(route('admin.support.show', $this->ticket))
            ->assertForbidden();
    }

    // =========================================================
    // Reply
    // =========================================================

    public function test_admin_can_reply_to_ticket(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.support.reply', $this->ticket), ['body' => 'Here is our response.'])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $this->ticket->id,
            'user_id'   => $this->admin->id,
            'body'      => 'Here is our response.',
        ]);
    }

    public function test_reply_dispatches_send_ticket_reply_job(): void
    {
        Queue::fake();

        $this->actingAs($this->admin)
            ->post(route('admin.support.reply', $this->ticket), ['body' => 'Dispatching reply.']);

        Queue::assertPushed(SendTicketReplyJob::class, fn($job) =>
            $job->ticket->id === $this->ticket->id
        );
    }

    public function test_reply_sets_open_ticket_to_pending(): void
    {
        $this->assertEquals('open', $this->ticket->status);

        $this->actingAs($this->admin)
            ->post(route('admin.support.reply', $this->ticket), ['body' => 'Updating status.']);

        $this->assertEquals('pending', $this->ticket->fresh()->status);
    }

    public function test_reply_does_not_change_status_if_already_pending(): void
    {
        $this->ticket->update(['status' => 'pending']);

        $this->actingAs($this->admin)
            ->post(route('admin.support.reply', $this->ticket), ['body' => 'Another reply.']);

        $this->assertEquals('pending', $this->ticket->fresh()->status);
    }

    public function test_reply_logs_to_admin_log(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.support.reply', $this->ticket), ['body' => 'Logging this.']);

        $this->assertDatabaseHas('admin_logs', [
            'admin_id'    => $this->admin->id,
            'action'      => 'reply_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $this->ticket->id,
        ]);
    }

    public function test_reply_requires_body(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.support.reply', $this->ticket), ['body' => ''])
            ->assertSessionHasErrors('body');
    }

    public function test_basic_user_cannot_post_admin_reply(): void
    {
        $this->actingAs($this->basicUser)
            ->post(route('admin.support.reply', $this->ticket), ['body' => 'Sneaky reply.'])
            ->assertForbidden();
    }

    // =========================================================
    // Assign
    // =========================================================

    public function test_admin_can_assign_ticket_to_themselves(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.support.assign', $this->ticket), ['assigned_to' => $this->admin->id])
            ->assertRedirect();

        $this->assertEquals($this->admin->id, $this->ticket->fresh()->assigned_to);
    }

    public function test_admin_can_unassign_ticket(): void
    {
        $this->ticket->update(['assigned_to' => $this->admin->id]);

        $this->actingAs($this->admin)
            ->patch(route('admin.support.assign', $this->ticket), ['assigned_to' => ''])
            ->assertRedirect();

        $this->assertNull($this->ticket->fresh()->assigned_to);
    }

    public function test_assigning_to_non_admin_user_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.support.assign', $this->ticket), ['assigned_to' => $this->basicUser->id])
            ->assertStatus(422);
    }

    public function test_assign_logs_to_admin_log(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.support.assign', $this->ticket), ['assigned_to' => $this->admin->id]);

        $this->assertDatabaseHas('admin_logs', [
            'admin_id'    => $this->admin->id,
            'action'      => 'assign_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $this->ticket->id,
        ]);
    }

    // =========================================================
    // Update Status
    // =========================================================

    public function test_admin_can_close_ticket(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.support.status', $this->ticket), ['status' => 'closed'])
            ->assertRedirect();

        $this->assertEquals('closed', $this->ticket->fresh()->status);
    }

    public function test_admin_can_reopen_closed_ticket(): void
    {
        $this->ticket->update(['status' => 'closed']);

        $this->actingAs($this->admin)
            ->patch(route('admin.support.status', $this->ticket), ['status' => 'open'])
            ->assertRedirect();

        $this->assertEquals('open', $this->ticket->fresh()->status);
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.support.status', $this->ticket), ['status' => 'deleted'])
            ->assertSessionHasErrors('status');
    }

    public function test_update_status_logs_to_admin_log(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.support.status', $this->ticket), ['status' => 'closed']);

        $log = AdminLog::where('target_id', $this->ticket->id)->latest()->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('ticket_status', $log->action);
        $this->assertStringContainsString('closed', $log->action);
    }

    public function test_basic_user_cannot_update_status(): void
    {
        $this->actingAs($this->basicUser)
            ->patch(route('admin.support.status', $this->ticket), ['status' => 'closed'])
            ->assertForbidden();
    }
}
