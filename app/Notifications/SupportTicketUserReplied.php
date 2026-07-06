<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SupportTicketUserReplied extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public SupportTicket $ticket)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->ticket->loadMissing('user');

        return [
            'admin_ticket_id' => $this->ticket->id,
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'user_name' => $this->ticket->user?->name,
            'message' => ($this->ticket->user?->name ?? 'A user') . ' replied to ticket "' . $this->ticket->subject . '".',
        ];
    }
}
