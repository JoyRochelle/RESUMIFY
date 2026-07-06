<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketAutoClosed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AutoCloseTicketsCommandTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $this->admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);
    }

    public function test_closes_ticket_whose_close_request_is_older_than_two_days(): void
    {
        $ticket = SupportTicket::factory()->create([
            'user_id'     => $this->owner->id,
            'assigned_to' => $this->admin->id,
            'status'      => 'open',
        ]);

        $this->travel(-3)->days();
        $ticket->requestClose($this->owner);
        $this->travelBack();

        $this->artisan('tickets:auto-close')->assertSuccessful();

        $ticket->refresh();
        $this->assertEquals('closed', $ticket->status);
        $this->assertNull($ticket->close_requested_by);
    }

    public function test_does_not_close_ticket_whose_close_request_is_younger_than_two_days(): void
    {
        $ticket = SupportTicket::factory()->create([
            'user_id'     => $this->owner->id,
            'assigned_to' => $this->admin->id,
            'status'      => 'open',
        ]);

        $this->travel(-1)->days();
        $ticket->requestClose($this->owner);
        $this->travelBack();

        $this->artisan('tickets:auto-close')->assertSuccessful();

        $ticket->refresh();
        $this->assertEquals('awaiting_closure', $ticket->status);
        $this->assertNotNull($ticket->close_requested_by);
    }

    public function test_does_not_touch_ticket_that_was_already_responded_to(): void
    {
        $ticket = SupportTicket::factory()->create([
            'user_id'     => $this->owner->id,
            'assigned_to' => $this->admin->id,
            'status'      => 'open',
        ]);

        $this->travel(-3)->days();
        $ticket->requestClose($this->owner);
        $this->travelBack();

        // Admin confirmed already — ticket is no longer awaiting_closure.
        $ticket->confirmClose();

        $this->artisan('tickets:auto-close')->assertSuccessful();

        $this->assertEquals('closed', $ticket->fresh()->status);
    }

    public function test_does_not_touch_open_tickets_without_a_close_request(): void
    {
        $ticket = SupportTicket::factory()->create([
            'user_id'     => $this->owner->id,
            'assigned_to' => $this->admin->id,
            'status'      => 'open',
        ]);

        $this->artisan('tickets:auto-close')->assertSuccessful();

        $this->assertEquals('open', $ticket->fresh()->status);
    }

    public function test_auto_close_notifies_both_owner_and_assigned_admin(): void
    {
        $ticket = SupportTicket::factory()->create([
            'user_id'     => $this->owner->id,
            'assigned_to' => $this->admin->id,
            'status'      => 'open',
        ]);

        $this->travel(-3)->days();
        $ticket->requestClose($this->owner);
        $this->travelBack();

        Notification::fake();

        $this->artisan('tickets:auto-close');

        Notification::assertSentTo($this->owner, SupportTicketAutoClosed::class);
        Notification::assertSentTo($this->admin, SupportTicketAutoClosed::class);
    }
}
