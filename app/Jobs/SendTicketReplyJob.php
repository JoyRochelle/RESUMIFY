<?php

namespace App\Jobs;

use App\Mail\TicketReplyMail;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendTicketReplyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 60, 300];

    public function __construct(
        public readonly SupportTicket $ticket,
        public readonly TicketReply $reply,
    ) {}

    public function handle(): void
    {
        $recipientEmail = $this->ticket->user?->email;

        if (! $recipientEmail) {
            return;
        }

        Mail::to($recipientEmail)
            ->send(new TicketReplyMail($this->ticket, $this->reply));
    }
}
