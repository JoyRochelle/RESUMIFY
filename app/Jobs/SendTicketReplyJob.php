<?php

namespace App\Jobs;

use App\Mail\TicketReplyMail;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendTicketReplyJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [10, 60, 300];

    public function __construct(
        public readonly SupportTicket $ticket,
        public readonly TicketReply $reply,
    ) {}

    public function handle(): void
    {
        Mail::to($this->ticket->user->email)->send(new TicketReplyMail($this->ticket, $this->reply));
    }
}
