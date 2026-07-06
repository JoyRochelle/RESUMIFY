<?php

namespace App\Livewire;

use App\Jobs\SendTicketReplyJob;
use App\Models\AdminLog;
use App\Models\SupportTicket;
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

    public function render(): View
    {
        $this->ticket->load('replies.sender:id,name,role,avatar_url');

        return view('livewire.ticket-chat');
    }
}
