<?php

namespace Tests\Feature;

use App\Models\AdminLog;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketCloseConfirmed;
use App\Notifications\SupportTicketCloseRejected;
use App\Notifications\SupportTicketCloseRequested;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AdminSupportTicketCloseFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $basicUser;
    private SupportTicket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin     = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $this->basicUser = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);

        $this->ticket = SupportTicket::factory()->create([
            'user_id'     => $this->basicUser->id,
            'subject'     => 'Cannot export PDF',
            'status'      => 'open',
            'assigned_to' => $this->admin->id,
        ]);
    }

    // =========================================================
    // Request close
    // =========================================================

    public function test_admin_can_request_close(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.support.request-close', $this->ticket))
            ->assertRedirect();

        $ticket = $this->ticket->fresh();
        $this->assertEquals('awaiting_closure', $ticket->status);
        $this->assertEquals($this->admin->id, $ticket->close_requested_by);
    }

    public function test_request_close_notifies_ticket_owner(): void
    {
        Notification::fake();

        $this->actingAs($this->admin)
            ->patch(route('admin.support.request-close', $this->ticket));

        Notification::assertSentTo($this->basicUser, SupportTicketCloseRequested::class);
    }

    public function test_request_close_logs_to_admin_log(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.support.request-close', $this->ticket));

        $this->assertDatabaseHas('admin_logs', [
            'admin_id'    => $this->admin->id,
            'action'      => 'request_close_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $this->ticket->id,
        ]);
    }

    public function test_basic_user_cannot_request_close_via_admin_route(): void
    {
        $this->actingAs($this->basicUser)
            ->patch(route('admin.support.request-close', $this->ticket))
            ->assertForbidden();
    }

    public function test_request_close_is_rejected_when_already_closed(): void
    {
        $this->ticket->update(['status' => 'closed']);

        $this->actingAs($this->admin)
            ->patch(route('admin.support.request-close', $this->ticket))
            ->assertStatus(422);
    }

    // =========================================================
    // Confirm close
    // =========================================================

    public function test_admin_can_confirm_close_requested_by_user(): void
    {
        $this->ticket->requestClose($this->basicUser);

        $this->actingAs($this->admin)
            ->patch(route('admin.support.confirm-close', $this->ticket))
            ->assertRedirect();

        $ticket = $this->ticket->fresh();
        $this->assertEquals('closed', $ticket->status);
        $this->assertNull($ticket->close_requested_by);
    }

    public function test_confirm_close_notifies_requester(): void
    {
        $this->ticket->requestClose($this->basicUser);
        Notification::fake();

        $this->actingAs($this->admin)
            ->patch(route('admin.support.confirm-close', $this->ticket));

        Notification::assertSentTo($this->basicUser, SupportTicketCloseConfirmed::class);
    }

    public function test_confirm_close_logs_to_admin_log(): void
    {
        $this->ticket->requestClose($this->basicUser);

        $this->actingAs($this->admin)
            ->patch(route('admin.support.confirm-close', $this->ticket));

        $this->assertDatabaseHas('admin_logs', [
            'admin_id'    => $this->admin->id,
            'action'      => 'confirm_close_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $this->ticket->id,
        ]);
    }

    public function test_admin_cannot_confirm_their_own_close_request(): void
    {
        $this->ticket->requestClose($this->admin);

        $this->actingAs($this->admin)
            ->patch(route('admin.support.confirm-close', $this->ticket))
            ->assertForbidden();

        $this->assertEquals('awaiting_closure', $this->ticket->fresh()->status);
    }

    public function test_confirm_close_fails_when_no_pending_request(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('admin.support.confirm-close', $this->ticket))
            ->assertStatus(422);
    }

    // =========================================================
    // Reject close
    // =========================================================

    public function test_admin_can_reject_close_requested_by_user(): void
    {
        $this->ticket->requestClose($this->basicUser);

        $this->actingAs($this->admin)
            ->patch(route('admin.support.reject-close', $this->ticket))
            ->assertRedirect();

        $ticket = $this->ticket->fresh();
        $this->assertEquals('open', $ticket->status);
        $this->assertNull($ticket->close_requested_by);
    }

    public function test_reject_close_notifies_requester(): void
    {
        $this->ticket->requestClose($this->basicUser);
        Notification::fake();

        $this->actingAs($this->admin)
            ->patch(route('admin.support.reject-close', $this->ticket));

        Notification::assertSentTo($this->basicUser, SupportTicketCloseRejected::class);
    }

    public function test_reject_close_logs_to_admin_log(): void
    {
        $this->ticket->requestClose($this->basicUser);

        $this->actingAs($this->admin)
            ->patch(route('admin.support.reject-close', $this->ticket));

        $this->assertDatabaseHas('admin_logs', [
            'admin_id'    => $this->admin->id,
            'action'      => 'reject_close_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $this->ticket->id,
        ]);
    }

    public function test_admin_cannot_reject_their_own_close_request(): void
    {
        $this->ticket->requestClose($this->admin);

        $this->actingAs($this->admin)
            ->patch(route('admin.support.reject-close', $this->ticket))
            ->assertForbidden();

        $this->assertEquals('awaiting_closure', $this->ticket->fresh()->status);
    }
}
