<?php

namespace App\Policies;

use App\Models\SupportTicket;
use App\Models\User;

class SupportTicketPolicy
{
    public function view(User $user, SupportTicket $ticket): bool
    {
        return $user->id === $ticket->user_id;
    }

    public function reply(User $user, SupportTicket $ticket): bool
    {
        return $user->id === $ticket->user_id || $user->isAdmin();
    }

    public function requestClose(User $user, SupportTicket $ticket): bool
    {
        return $user->isAdmin();
    }

    public function confirmClose(User $user, SupportTicket $ticket): bool
    {
        return ($user->id === $ticket->user_id || $user->isAdmin())
            && $ticket->close_requested_by !== $user->id;
    }

    public function rejectClose(User $user, SupportTicket $ticket): bool
    {
        return $this->confirmClose($user, $ticket);
    }
}
