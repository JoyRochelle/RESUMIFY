<?php

namespace Tests\Feature;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\SupportTicketUserReplied;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserTicketReplyTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $otherUser;
    private User $admin;
    private SupportTicket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner     = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $this->otherUser = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $this->admin     = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->ticket = SupportTicket::factory()->create([
            'user_id'     => $this->owner->id,
            'subject'     => 'Cannot export PDF',
            'status'      => 'open',
            'assigned_to' => $this->admin->id,
        ]);
    }

    // =========================================================
    // Success
    // =========================================================

    public function test_owner_can_reply_to_their_open_ticket(): void
    {
        $this->actingAs($this->owner)
            ->post(route('help.tickets.reply', $this->ticket), ['body' => 'Any update on this?'])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $this->ticket->id,
            'user_id'   => $this->owner->id,
            'body'      => 'Any update on this?',
        ]);
    }

    public function test_owner_reply_moves_pending_ticket_back_to_open(): void
    {
        $this->ticket->update(['status' => 'pending']);

        $this->actingAs($this->owner)
            ->post(route('help.tickets.reply', $this->ticket), ['body' => 'Following up.']);

        $this->assertEquals('open', $this->ticket->fresh()->status);
    }

    public function test_owner_reply_does_not_change_status_if_already_open(): void
    {
        $this->actingAs($this->owner)
            ->post(route('help.tickets.reply', $this->ticket), ['body' => 'First follow-up.']);

        $this->assertEquals('open', $this->ticket->fresh()->status);
    }

    public function test_reply_redirects_back_to_ticket_show(): void
    {
        $this->actingAs($this->owner)
            ->post(route('help.tickets.reply', $this->ticket), ['body' => 'Any update on this?'])
            ->assertRedirect(route('help.tickets.show', $this->ticket));
    }

    // =========================================================
    // Authorization
    // =========================================================

    public function test_non_owner_cannot_reply_to_ticket(): void
    {
        $this->actingAs($this->otherUser)
            ->post(route('help.tickets.reply', $this->ticket), ['body' => 'Sneaky reply.'])
            ->assertForbidden();

        $this->assertDatabaseMissing('ticket_replies', [
            'ticket_id' => $this->ticket->id,
            'user_id'   => $this->otherUser->id,
        ]);
    }

    public function test_guest_cannot_reply_to_ticket(): void
    {
        $this->post(route('help.tickets.reply', $this->ticket), ['body' => 'Guest attempt.'])
            ->assertRedirect(route('login'));
    }

    // =========================================================
    // Closed ticket rejection
    // =========================================================

    public function test_reply_is_rejected_when_ticket_is_closed(): void
    {
        $this->ticket->update(['status' => 'closed']);

        $this->actingAs($this->owner)
            ->post(route('help.tickets.reply', $this->ticket), ['body' => 'Reopening?'])
            ->assertSessionHas('error');

        $this->assertDatabaseMissing('ticket_replies', [
            'ticket_id' => $this->ticket->id,
            'body'      => 'Reopening?',
        ]);
    }

    // =========================================================
    // Validation
    // =========================================================

    public function test_reply_requires_body(): void
    {
        $this->actingAs($this->owner)
            ->post(route('help.tickets.reply', $this->ticket), ['body' => ''])
            ->assertSessionHasErrors('body');
    }

    public function test_reply_body_max_length(): void
    {
        $this->actingAs($this->owner)
            ->post(route('help.tickets.reply', $this->ticket), ['body' => str_repeat('a', 5001)])
            ->assertSessionHasErrors('body');
    }

    // =========================================================
    // Notifications
    // =========================================================

    public function test_assigned_admin_is_notified_when_owner_replies(): void
    {
        Notification::fake();

        $this->actingAs($this->owner)
            ->post(route('help.tickets.reply', $this->ticket), ['body' => 'Any update on this?']);

        Notification::assertSentTo(
            $this->admin,
            SupportTicketUserReplied::class,
            fn ($notification) => $notification->ticket->id === $this->ticket->id
        );
    }

    public function test_no_notification_sent_when_ticket_has_no_assigned_admin(): void
    {
        $this->ticket->update(['assigned_to' => null]);
        Notification::fake();

        $this->actingAs($this->owner)
            ->post(route('help.tickets.reply', $this->ticket), ['body' => 'Any update on this?']);

        Notification::assertNothingSent();
    }
}
