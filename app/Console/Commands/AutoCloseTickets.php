<?php

namespace App\Console\Commands;

use App\Models\SupportTicket;
use App\Notifications\SupportTicketAutoClosed;
use Illuminate\Console\Command;

class AutoCloseTickets extends Command
{
    protected $signature = 'tickets:auto-close';

    protected $description = 'Auto-close support tickets whose close request has gone unanswered for over 2 days';

    public function handle(): int
    {
        $count = 0;

        SupportTicket::query()
            ->with(['user', 'assignedAdmin'])
            ->where('status', 'awaiting_closure')
            ->where('close_requested_at', '<=', now()->subDays(2))
            ->each(function (SupportTicket $ticket) use (&$count) {
                $ticket->confirmClose();

                $ticket->user?->notify(new SupportTicketAutoClosed($ticket));
                $ticket->assignedAdmin?->notify(new SupportTicketAutoClosed($ticket));

                $count++;
            });

        $this->info("Auto-closed {$count} ticket(s).");

        return self::SUCCESS;
    }
}
