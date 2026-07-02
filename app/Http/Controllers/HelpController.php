<?php

namespace App\Http\Controllers;

use App\Jobs\SendTicketReplyJob;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\NewSupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class HelpController extends Controller
{
    public function index(): View
    {
        return view('user.help');
    }

    public function contact(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'subject' => $data['subject'],
            'status'  => 'open',
        ]);

        $reply = TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id'   => auth()->id(),
            'body'      => $data['message'],
        ]);

        dispatch(new SendTicketReplyJob($ticket, $reply));

        $admins = User::where('role', 'admin')->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewSupportTicket($ticket->loadMissing('user')));
        }

        return redirect()->route('user.help')->with('success', 'Your message has been sent! We\'ll get back to you soon.');
    }

    public function tickets(): View
    {
        $tickets = auth()->user()
            ->supportTickets()
            ->latest()
            ->paginate(10);

        return view('user.tickets', compact('tickets'));
    }

    public function showTicket(SupportTicket $ticket): View
    {
        if ($ticket->user_id !== auth()->id()) {
            abort(403);
        }

        $ticket->load('replies.sender');

        return view('user.tickets.show', compact('ticket'));
    }
}
