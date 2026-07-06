<?php

namespace App\Livewire;

use App\Jobs\SendTicketReplyJob;
use App\Models\AdminLog;
use App\Models\SupportTicket;
use App\Notifications\SupportTicketCloseConfirmed;
use App\Notifications\SupportTicketCloseRejected;
use App\Notifications\SupportTicketCloseRequested;
use App\Notifications\SupportTicketReplied;
use App\Notifications\SupportTicketUserReplied;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Livewire\Component;

class TicketChat extends Component
{
    public SupportTicket $ticket;

    public string $body = '';

    public function mount(SupportTicket $ticket): void
    {
        $this->ticket = $ticket;
    }

    public function sendReply(): void
    {
        Gate::authorize('reply', $this->ticket);

        if ($this->ticket->status === 'closed') {
            $this->addError('body', 'This ticket is closed and cannot receive new replies.');

            return;
        }

        $validated = $this->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $author = auth()->user();

        $reply = $this->ticket->replies()->create([
            'user_id' => $author->id,
            'body'    => $validated['body'],
        ]);

        if ($author->isAdmin()) {
            if ($this->ticket->status === 'open') {
                $this->ticket->update(['status' => 'pending']);
            }

            SendTicketReplyJob::dispatch($this->ticket, $reply);

            $this->ticket->user?->notify(new SupportTicketReplied($this->ticket));

            AdminLog::create([
                'admin_id'    => $author->id,
                'action'      => 'reply_ticket',
                'target_type' => 'support_ticket',
                'target_id'   => $this->ticket->id,
            ]);
        } else {
            if ($this->ticket->status === 'pending') {
                $this->ticket->update(['status' => 'open']);
            }

            $this->ticket->assignedAdmin?->notify(new SupportTicketUserReplied($this->ticket));
        }

        $this->body = '';
        $this->ticket->refresh();
    }

    public function requestClose(): void
    {
        Gate::authorize('requestClose', $this->ticket);

        if (in_array($this->ticket->status, ['awaiting_closure', 'closed'], true)) {
            $this->addError('close', 'A close request is already pending or the ticket is already closed.');

            return;
        }

        $author = auth()->user();

        $this->ticket->requestClose($author);

        $this->ticket->otherParty($author)?->notify(new SupportTicketCloseRequested($this->ticket));

        if ($author->isAdmin()) {
            AdminLog::create([
                'admin_id'    => $author->id,
                'action'      => 'request_close_ticket',
                'target_type' => 'support_ticket',
                'target_id'   => $this->ticket->id,
            ]);
        }

        $this->ticket->refresh();
    }

    public function confirmClose(): void
    {
        Gate::authorize('confirmClose', $this->ticket);

        if ($this->ticket->status !== 'awaiting_closure') {
            $this->addError('close', 'There is no pending close request to confirm.');

            return;
        }

        $author = auth()->user();
        $requester = $this->ticket->closeRequestedBy;

        $this->ticket->confirmClose();

        $requester?->notify(new SupportTicketCloseConfirmed($this->ticket));

        if ($author->isAdmin()) {
            AdminLog::create([
                'admin_id'    => $author->id,
                'action'      => 'confirm_close_ticket',
                'target_type' => 'support_ticket',
                'target_id'   => $this->ticket->id,
            ]);
        }

        $this->ticket->refresh();
    }

    public function rejectClose(): void
    {
        Gate::authorize('rejectClose', $this->ticket);

        if ($this->ticket->status !== 'awaiting_closure') {
            $this->addError('close', 'There is no pending close request to reject.');

            return;
        }

        $author = auth()->user();
        $requester = $this->ticket->closeRequestedBy;

        $this->ticket->rejectClose();

        $requester?->notify(new SupportTicketCloseRejected($this->ticket));

        if ($author->isAdmin()) {
            AdminLog::create([
                'admin_id'    => $author->id,
                'action'      => 'reject_close_ticket',
                'target_type' => 'support_ticket',
                'target_id'   => $this->ticket->id,
            ]);
        }

        $this->ticket->refresh();
    }

    public function render(): View
    {
        $this->ticket->load('replies.sender:id,name,role,avatar_url');

        return view('livewire.ticket-chat');
    }
}
