<?php

namespace Tests\Feature;

use App\Livewire\TicketChat;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketCloseConfirmed;
use App\Notifications\SupportTicketCloseRejected;
use App\Notifications\SupportTicketCloseRequested;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class TicketMutualCloseFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $admin;
    private User $otherUser;
    private SupportTicket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner     = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $this->admin     = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
        $this->otherUser = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);

        $this->ticket = SupportTicket::factory()->create([
            'user_id'     => $this->owner->id,
            'subject'     => 'Cannot export PDF',
            'status'      => 'open',
            'assigned_to' => $this->admin->id,
        ]);
    }

    // =========================================================
    // Only admins may initiate a close request
    // =========================================================

    public function test_owner_cannot_request_close(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->call('requestClose')
            ->assertForbidden();

        $this->assertEquals('open', $this->ticket->fresh()->status);
    }

    // =========================================================
    // Admin requests, owner confirms
    // =========================================================

    public function test_owner_can_confirm_close_requested_by_admin(): void
    {
        $this->ticket->requestClose($this->admin);
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->call('confirmClose')
            ->assertHasNoErrors();

        $this->ticket->refresh();
        $this->assertEquals('closed', $this->ticket->status);
        $this->assertNull($this->ticket->close_requested_by);
    }

    public function test_owner_confirming_admin_request_notifies_admin(): void
    {
        $this->ticket->requestClose($this->admin);
        Notification::fake();
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->call('confirmClose');

        Notification::assertSentTo($this->admin, SupportTicketCloseConfirmed::class);
    }

    // =========================================================
    // Admin requests, owner rejects
    // =========================================================

    public function test_admin_can_request_close(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->call('requestClose')
            ->assertHasNoErrors();

        $this->ticket->refresh();
        $this->assertEquals('awaiting_closure', $this->ticket->status);
        $this->assertEquals($this->admin->id, $this->ticket->close_requested_by);
    }

    public function test_admin_request_close_notifies_ticket_owner(): void
    {
        Notification::fake();
        $this->actingAs($this->admin);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->call('requestClose');

        Notification::assertSentTo($this->owner, SupportTicketCloseRequested::class);
    }

    public function test_owner_can_reject_close_requested_by_admin(): void
    {
        $this->ticket->requestClose($this->admin);
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->call('rejectClose')
            ->assertHasNoErrors();

        $this->ticket->refresh();
        $this->assertEquals('open', $this->ticket->status);
        $this->assertNull($this->ticket->close_requested_by);
    }

    public function test_owner_rejecting_admin_request_notifies_admin(): void
    {
        $this->ticket->requestClose($this->admin);
        Notification::fake();
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->call('rejectClose');

        Notification::assertSentTo($this->admin, SupportTicketCloseRejected::class);
    }

    // =========================================================
    // Self-confirm is forbidden
    // =========================================================

    public function test_owner_cannot_confirm_their_own_close_request(): void
    {
        $this->ticket->requestClose($this->owner);
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->call('confirmClose')
            ->assertForbidden();

        $this->assertEquals('awaiting_closure', $this->ticket->fresh()->status);
    }

    public function test_owner_cannot_reject_their_own_close_request(): void
    {
        $this->ticket->requestClose($this->owner);
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->call('rejectClose')
            ->assertForbidden();

        $this->assertEquals('awaiting_closure', $this->ticket->fresh()->status);
    }

    public function test_admin_cannot_confirm_their_own_close_request(): void
    {
        $this->ticket->requestClose($this->admin);
        $this->actingAs($this->admin);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->call('confirmClose')
            ->assertForbidden();

        $this->assertEquals('awaiting_closure', $this->ticket->fresh()->status);
    }

    // =========================================================
    // Authorization for unrelated users
    // =========================================================

    public function test_unrelated_user_cannot_request_close(): void
    {
        $this->actingAs($this->otherUser);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->call('requestClose')
            ->assertForbidden();

        $this->assertEquals('open', $this->ticket->fresh()->status);
    }

    public function test_unrelated_user_cannot_confirm_close(): void
    {
        $this->ticket->requestClose($this->admin);
        $this->actingAs($this->otherUser);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->call('confirmClose')
            ->assertForbidden();
    }

    // =========================================================
    // No pending request to act on
    // =========================================================

    public function test_confirm_close_fails_when_no_pending_request(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->call('confirmClose')
            ->assertHasErrors('close');

        $this->assertEquals('open', $this->ticket->fresh()->status);
    }

    public function test_request_close_fails_when_already_closed(): void
    {
        $this->ticket->update(['status' => 'closed']);
        $this->actingAs($this->admin);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->call('requestClose')
            ->assertHasErrors('close');

        $this->assertEquals('closed', $this->ticket->fresh()->status);
    }
}
