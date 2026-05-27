<?php

namespace App\Mail;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly SupportTicket $ticket,
        public readonly TicketReply $reply,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: [Ticket #' . substr($this->ticket->id, -8) . '] ' . $this->ticket->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-reply',
            with: [
                'ticket' => $this->ticket,
                'reply'  => $this->reply,
            ],
        );
    }
}
