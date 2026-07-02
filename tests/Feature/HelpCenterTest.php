<?php

namespace Tests\Feature;

use App\Jobs\SendTicketReplyJob;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class HelpCenterTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user      = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
        $this->otherUser = User::factory()->create(['role' => 'basic', 'email_verified_at' => now()]);
    }

    // =========================================================
    // Help Index
    // =========================================================

    public function test_authenticated_user_can_view_help_page(): void
    {
        $this->actingAs($this->user)
            ->get(route('user.help'))
            ->assertOk()
            ->assertViewIs('user.help');
    }

    public function test_guest_is_redirected_from_help_page(): void
    {
        $this->get(route('user.help'))
            ->assertRedirect(route('login'));
    }

    public function test_help_page_shows_success_flash(): void
    {
        $this->actingAs($this->user)
            ->withSession(['success' => 'Your message has been sent!'])
            ->get(route('user.help'))
            ->assertSee('Your message has been sent!');
    }

    // =========================================================
    // Contact Form — POST /help/contact
    // =========================================================

    public function test_user_can_submit_contact_form(): void
    {
        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => 'Cannot export PDF',
                'message' => 'I keep getting an error when trying to export.',
            ])
            ->assertRedirect(route('user.help'));
    }

    public function test_contact_creates_support_ticket(): void
    {
        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => 'PDF export broken',
                'message' => 'Detailed description here.',
            ]);

        $this->assertDatabaseHas('support_tickets', [
            'user_id' => $this->user->id,
            'subject' => 'PDF export broken',
            'status'  => 'open',
        ]);
    }

    public function test_contact_creates_initial_ticket_reply(): void
    {
        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => 'Need help with AI',
                'message' => 'My AI credits are not updating.',
            ]);

        $ticket = SupportTicket::where('user_id', $this->user->id)->first();
        $this->assertNotNull($ticket);

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id'   => $this->user->id,
            'body'      => 'My AI credits are not updating.',
        ]);
    }

    public function test_contact_dispatches_send_ticket_reply_job(): void
    {
        Queue::fake();

        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => 'Job dispatch test',
                'message' => 'Please reply to this.',
            ]);

        Queue::assertPushed(SendTicketReplyJob::class, fn($job) =>
            $job->ticket->user_id === $this->user->id
        );
    }

    public function test_contact_notifies_admins_about_new_ticket(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => 'Admin notification test',
                'message' => 'Please alert the admin.',
            ]);

        $ticket = SupportTicket::where('subject', 'Admin notification test')->first();
        $notification = $admin->notifications()->first();

        $this->assertNotNull($ticket);
        $this->assertNotNull($notification);
        $this->assertSame($ticket->id, $notification->data['admin_ticket_id']);
        $this->assertStringContainsString('Admin notification test', $notification->data['message']);
    }

    public function test_admin_notification_read_redirects_to_admin_ticket_show(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email_verified_at' => now()]);

        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => 'Admin redirect notification',
                'message' => 'Open this from the admin navbar.',
            ]);

        $ticket = SupportTicket::where('subject', 'Admin redirect notification')->firstOrFail();
        $notification = $admin->notifications()->firstOrFail();

        $this->actingAs($admin)
            ->get(route('notifications.read', $notification->id))
            ->assertRedirect(route('admin.support.show', $ticket));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_contact_redirects_with_success_flash(): void
    {
        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => 'Flash message test',
                'message' => 'This is the message body.',
            ])
            ->assertRedirect(route('user.help'))
            ->assertSessionHas('success');
    }

    public function test_contact_requires_subject(): void
    {
        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => '',
                'message' => 'Message without subject.',
            ])
            ->assertSessionHasErrors('subject');
    }

    public function test_contact_requires_message(): void
    {
        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => 'Subject without message',
                'message' => '',
            ])
            ->assertSessionHasErrors('message');
    }

    public function test_contact_subject_max_length(): void
    {
        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => str_repeat('a', 256),
                'message' => 'Valid message body.',
            ])
            ->assertSessionHasErrors('subject');
    }

    public function test_contact_message_max_length(): void
    {
        $this->actingAs($this->user)
            ->post(route('help.contact'), [
                'subject' => 'Valid subject',
                'message' => str_repeat('a', 5001),
            ])
            ->assertSessionHasErrors('message');
    }

    public function test_guest_cannot_submit_contact_form(): void
    {
        $this->post(route('help.contact'), [
            'subject' => 'Guest attempt',
            'message' => 'Trying without auth.',
        ])
        ->assertRedirect(route('login'));
    }

    // =========================================================
    // Ticket List — GET /help/tickets
    // =========================================================

    public function test_user_can_view_their_ticket_list(): void
    {
        SupportTicket::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->get(route('help.tickets'))
            ->assertOk()
            ->assertViewIs('user.tickets');
    }

    public function test_guest_is_redirected_from_ticket_list(): void
    {
        $this->get(route('help.tickets'))
            ->assertRedirect(route('login'));
    }

    public function test_ticket_list_only_shows_own_tickets(): void
    {
        $ownTicket   = SupportTicket::factory()->create(['user_id' => $this->user->id, 'subject' => 'My ticket']);
        $otherTicket = SupportTicket::factory()->create(['user_id' => $this->otherUser->id, 'subject' => 'Other ticket']);

        $response = $this->actingAs($this->user)->get(route('help.tickets'));

        $subjects = $response->viewData('tickets')->pluck('subject');
        $this->assertTrue($subjects->contains('My ticket'));
        $this->assertFalse($subjects->contains('Other ticket'));
    }

    public function test_ticket_list_passes_tickets_variable(): void
    {
        $this->actingAs($this->user)
            ->get(route('help.tickets'))
            ->assertViewHas('tickets');
    }

    public function test_ticket_list_paginates_at_10(): void
    {
        SupportTicket::factory()->count(15)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get(route('help.tickets'));

        $this->assertLessThanOrEqual(10, $response->viewData('tickets')->count());
    }

    public function test_ticket_list_uses_bounded_queries_when_rendering_reply_counts(): void
    {
        SupportTicket::factory()
            ->count(10)
            ->create(['user_id' => $this->user->id])
            ->each(function (SupportTicket $ticket): void {
                TicketReply::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $this->user->id,
                    'body'      => 'Initial support message.',
                ]);

                TicketReply::create([
                    'ticket_id' => $ticket->id,
                    'user_id'   => $this->user->id,
                    'body'      => 'Follow-up support message.',
                ]);
            });

        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->actingAs($this->user)
            ->get(route('help.tickets'))
            ->assertOk();

        $this->assertLessThanOrEqual(8, count(DB::getQueryLog()));
    }

    // =========================================================
    // Ticket Show — GET /help/tickets/{ticket}
    // =========================================================

    public function test_user_can_view_their_own_ticket(): void
    {
        $ticket = SupportTicket::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->get(route('help.tickets.show', $ticket))
            ->assertOk()
            ->assertViewIs('user.tickets.show');
    }

    public function test_guest_is_redirected_from_ticket_show(): void
    {
        $ticket = SupportTicket::factory()->create(['user_id' => $this->user->id]);

        $this->get(route('help.tickets.show', $ticket))
            ->assertRedirect(route('login'));
    }

    public function test_user_cannot_view_another_users_ticket(): void
    {
        $ticket = SupportTicket::factory()->create(['user_id' => $this->otherUser->id]);

        $this->actingAs($this->user)
            ->get(route('help.tickets.show', $ticket))
            ->assertForbidden();
    }

    public function test_ticket_show_passes_ticket_variable(): void
    {
        $ticket = SupportTicket::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->get(route('help.tickets.show', $ticket))
            ->assertViewHas('ticket');
    }

    public function test_ticket_show_loads_replies(): void
    {
        $ticket = SupportTicket::factory()->create(['user_id' => $this->user->id]);
        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => $this->user->id,
            'body'      => 'Initial message body.',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('help.tickets.show', $ticket));

        $this->assertEquals(1, $response->viewData('ticket')->replies->count());
    }
}
