<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SupportTicketCloseConfirmed extends Notification implements ShouldQueue
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
        return [
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'message' => 'Your close request for ticket "' . $this->ticket->subject . '" was confirmed. The ticket is now closed.',
        ];
    }
}
