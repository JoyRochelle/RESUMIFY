<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Jobs\SendTicketReplyJob;
use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use App\Notifications\NewSupportTicket;
use App\Notifications\SupportTicketUserReplied;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        return redirect()->route('user.help')->with('success', __('messages.help.contact.success'));
    }

    public function tickets(): View
    {
        $tickets = auth()->user()
            ->supportTickets()
            ->withCount('replies')
            ->latest()
            ->paginate(10);

        return view('user.tickets', compact('tickets'));
    }

    public function showTicket(SupportTicket $ticket): View
    {
        Gate::authorize('view', $ticket);

        $ticket->load('replies.sender');

        return view('user.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        Gate::authorize('reply', $ticket);

        if ($ticket->status === 'closed') {
            return back()->with('error', __('messages.tickets.chat.closed_reply_error'));
        }

        $data = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'body'    => $data['body'],
        ]);

        if ($ticket->status === 'pending') {
            $ticket->update(['status' => 'open']);
        }

        if ($ticket->assignedAdmin) {
            $ticket->assignedAdmin->notify(new SupportTicketUserReplied($ticket));
        }

        return redirect()->route('help.tickets.show', $ticket)->with('success', __('messages.tickets.chat.reply_sent'));
    }
}
