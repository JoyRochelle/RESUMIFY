<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function read($id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id);
        $notification->markAsRead();

        if ($user->isAdmin() && isset($notification->data['admin_ticket_id'])) {
            return redirect()->route('admin.support.show', $notification->data['admin_ticket_id']);
        }

        if (!$user->isAdmin() && isset($notification->data['ticket_id'])) {
            return redirect()->route('help.tickets.show', $notification->data['ticket_id']);
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return back();
    }

    public function readAll()
    {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    }
}
