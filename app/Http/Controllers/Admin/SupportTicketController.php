<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendTicketReplyJob;
use App\Models\AdminLog;
use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\SupportTicketCloseConfirmed;
use App\Notifications\SupportTicketCloseRejected;
use App\Notifications\SupportTicketCloseRequested;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    public function index(Request $request): View
    {
        $status  = $request->input('status');
        $search  = $request->input('search');

        $tickets = SupportTicket::query()
            ->with(['user:id,name,email', 'assignedAdmin:id,name'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($search, function ($query, string $search): void {
                $likeSearch = "%{$search}%";

                $query->where(function ($query) use ($likeSearch): void {
                    $query->whereHas('user', function ($userQuery) use ($likeSearch): void {
                        $userQuery
                            ->where('name', 'like', $likeSearch)
                            ->orWhere('email', 'like', $likeSearch);
                    })->orWhere('subject', 'like', $likeSearch);
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $openCount    = SupportTicket::where('status', 'open')->count();
        $pendingCount = SupportTicket::where('status', 'pending')->count();
        $closedCount  = SupportTicket::where('status', 'closed')->count();

        $admins = User::where('role', 'admin')->orderBy('name')->get(['id', 'name']);

        return view('admin.support', compact(
            'tickets', 'openCount', 'pendingCount', 'closedCount',
            'status', 'search', 'admins'
        ));
    }

    public function show(SupportTicket $ticket): View
    {
        $ticket->load([
            'user:id,name,email,avatar_url',
            'replies.sender:id,name,role,avatar_url',
            'assignedAdmin:id,name',
        ]);

        $admins = User::where('role', 'admin')->orderBy('name')->get(['id', 'name']);

        return view('admin.support.show', compact('ticket', 'admins'));
    }

    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate(['body' => 'required|string|max:5000']);

        $reply = $ticket->replies()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        if ($ticket->status === 'open') {
            $ticket->update(['status' => 'pending']);
        }

        SendTicketReplyJob::dispatch($ticket, $reply);

        if ($ticket->user) {
            $ticket->user->notify(new \App\Notifications\SupportTicketReplied($ticket));
        }

        AdminLog::create([
            'admin_id'    => auth()->id(),
            'action'      => 'reply_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $ticket->id,
        ]);

        return back()->with('success', 'Reply sent.');
    }

    public function assign(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        // Ensure the target user is actually an admin
        if ($request->filled('assigned_to')) {
            $target = User::findOrFail($request->assigned_to);
            abort_if(! $target->isAdmin(), 422, 'Can only assign to admin users.');
        }

        $ticket->update(['assigned_to' => $request->assigned_to ?: null]);

        AdminLog::create([
            'admin_id'    => auth()->id(),
            'action'      => 'assign_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $ticket->id,
        ]);

        return back()->with('success', 'Ticket assigned.');
    }

    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:open,pending,closed',
        ]);

        $ticket->update(['status' => $request->status]);

        AdminLog::create([
            'admin_id'    => auth()->id(),
            'action'      => "ticket_status:{$request->status}",
            'target_type' => 'support_ticket',
            'target_id'   => $ticket->id,
        ]);

        return back()->with('success', 'Status updated to ' . $request->status . '.');
    }

    public function requestClose(SupportTicket $ticket): RedirectResponse
    {
        Gate::authorize('requestClose', $ticket);

        abort_if(
            in_array($ticket->status, ['awaiting_closure', 'closed'], true),
            422,
            'A close request is already pending or the ticket is already closed.'
        );

        $admin = auth()->user();

        $ticket->requestClose($admin);

        $ticket->otherParty($admin)?->notify(new SupportTicketCloseRequested($ticket));

        AdminLog::create([
            'admin_id'    => $admin->id,
            'action'      => 'request_close_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $ticket->id,
        ]);

        return back()->with('success', 'Close requested.');
    }

    public function confirmClose(SupportTicket $ticket): RedirectResponse
    {
        Gate::authorize('confirmClose', $ticket);

        abort_if($ticket->status !== 'awaiting_closure', 422, 'There is no pending close request to confirm.');

        $admin = auth()->user();
        $requester = $ticket->closeRequestedBy;

        $ticket->confirmClose();

        $requester?->notify(new SupportTicketCloseConfirmed($ticket));

        AdminLog::create([
            'admin_id'    => $admin->id,
            'action'      => 'confirm_close_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $ticket->id,
        ]);

        return back()->with('success', 'Ticket closed.');
    }

    public function rejectClose(SupportTicket $ticket): RedirectResponse
    {
        Gate::authorize('rejectClose', $ticket);

        abort_if($ticket->status !== 'awaiting_closure', 422, 'There is no pending close request to reject.');

        $admin = auth()->user();
        $requester = $ticket->closeRequestedBy;

        $ticket->rejectClose();

        $requester?->notify(new SupportTicketCloseRejected($ticket));

        AdminLog::create([
            'admin_id'    => $admin->id,
            'action'      => 'reject_close_ticket',
            'target_type' => 'support_ticket',
            'target_id'   => $ticket->id,
        ]);

        return back()->with('success', 'Close request rejected.');
    }
}
