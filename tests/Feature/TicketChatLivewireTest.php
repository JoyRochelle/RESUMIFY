<?php

namespace Tests\Feature;

use App\Livewire\TicketChat;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\SupportTicketReplied;
use App\Notifications\SupportTicketUserReplied;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class TicketChatLivewireTest extends TestCase
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
    // Rendering
    // =========================================================

    public function test_component_renders_existing_thread(): void
    {
        TicketReply::create([
            'ticket_id' => $this->ticket->id,
            'user_id'   => $this->owner->id,
            'body'      => 'Initial message body.',
        ]);

        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->assertSee('Initial message body.');
    }

    public function test_component_shows_reply_form_for_open_ticket(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->assertSeeHtml('wire:submit="sendReply"');
    }

    public function test_component_hides_reply_form_for_closed_ticket(): void
    {
        $this->ticket->update(['status' => 'closed']);
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->assertDontSeeHtml('wire:submit="sendReply"')
            ->assertSee('This ticket is closed.');
    }

    public function test_ticket_show_page_polls_for_updates(): void
    {
        $this->actingAs($this->owner)
            ->get(route('help.tickets.show', $this->ticket))
            ->assertSee('wire:poll.7s', false)
            ->assertSee('max-w-5xl', false);
    }

    public function test_conversation_thread_has_no_message_dividers(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->assertDontSeeHtml('divide-y');
    }

    public function test_conversation_thread_autoscrolls_when_messages_change(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->assertSeeHtml('x-ref="thread"')
            ->assertSeeHtml('MutationObserver')
            ->assertSeeHtml('scrollThread');
    }

    public function test_reply_box_sends_with_enter_and_keeps_shift_enter_for_new_lines(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->assertSeeHtml('x-on:keydown.enter="submitReplyFromKeyboard($event)"')
            ->assertSeeHtml('event.shiftKey');
    }

    // =========================================================
    // Sending replies — owner
    // =========================================================

    public function test_owner_can_send_a_reply(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->set('body', 'Any update on this?')
            ->call('sendReply')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $this->ticket->id,
            'user_id'   => $this->owner->id,
            'body'      => 'Any update on this?',
        ]);
    }

    public function test_body_field_clears_after_successful_send(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->set('body', 'Any update on this?')
            ->call('sendReply')
            ->assertSet('body', '')
            ->assertDispatched('ticket-reply-sent');
    }

    public function test_reply_form_listens_for_successful_send_clear_event(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->assertSeeHtml('x-on:ticket-reply-sent.window');
    }

    public function test_owner_reply_moves_pending_ticket_back_to_open(): void
    {
        $this->ticket->update(['status' => 'pending']);
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->set('body', 'Following up.')
            ->call('sendReply');

        $this->assertEquals('open', $this->ticket->fresh()->status);
    }

    public function test_owner_reply_notifies_assigned_admin(): void
    {
        Notification::fake();
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->set('body', 'Any update on this?')
            ->call('sendReply');

        Notification::assertSentTo($this->admin, SupportTicketUserReplied::class);
    }

    // =========================================================
    // Sending replies — admin
    // =========================================================

    public function test_admin_can_send_a_reply(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->set('body', 'We are looking into it.')
            ->call('sendReply')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $this->ticket->id,
            'user_id'   => $this->admin->id,
            'body'      => 'We are looking into it.',
        ]);
    }

    public function test_admin_reply_sets_open_ticket_to_pending(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->set('body', 'We are looking into it.')
            ->call('sendReply');

        $this->assertEquals('pending', $this->ticket->fresh()->status);
    }

    public function test_admin_reply_notifies_ticket_owner(): void
    {
        Notification::fake();
        $this->actingAs($this->admin);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->set('body', 'We are looking into it.')
            ->call('sendReply');

        Notification::assertSentTo($this->owner, SupportTicketReplied::class);
    }

    public function test_admin_reply_logs_to_admin_log(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->set('body', 'We are looking into it.')
            ->call('sendReply');

        $this->assertDatabaseHas('admin_logs', [
            'admin_id'    => $this->admin->id,
            'action'      => 'reply_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $this->ticket->id,
        ]);
    }

    // =========================================================
    // Authorization & validation
    // =========================================================

    public function test_non_owner_non_admin_cannot_send_a_reply(): void
    {
        $this->actingAs($this->otherUser);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->set('body', 'Sneaky reply.')
            ->call('sendReply')
            ->assertForbidden();

        $this->assertDatabaseMissing('ticket_replies', [
            'ticket_id' => $this->ticket->id,
            'user_id'   => $this->otherUser->id,
        ]);
    }

    public function test_reply_is_rejected_when_ticket_closed(): void
    {
        $this->ticket->update(['status' => 'closed']);
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket->fresh()])
            ->set('body', 'Reopening?')
            ->call('sendReply')
            ->assertHasErrors('body');

        $this->assertDatabaseMissing('ticket_replies', [
            'ticket_id' => $this->ticket->id,
            'body'      => 'Reopening?',
        ]);
    }

    public function test_reply_requires_body(): void
    {
        $this->actingAs($this->owner);

        Livewire::test(TicketChat::class, ['ticket' => $this->ticket])
            ->set('body', '')
            ->call('sendReply')
            ->assertHasErrors('body');
    }
}
