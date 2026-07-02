<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewSupportTicket extends Notification
{
    use Queueable;

    public function __construct(public SupportTicket $ticket)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
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
            'message' => 'New support ticket from ' . ($this->ticket->user?->name ?? 'a user') . ': ' . $this->ticket->subject,
        ];
    }
}
