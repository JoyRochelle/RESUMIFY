<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function cancel(Request $request): RedirectResponse
    {
        $user = $request->user();

        $subscription = $user->subscriptions()
            ->where('plan', 'premium')
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->latest('created_at')
            ->first();

        if (!$subscription) {
            return redirect()
                ->route('user.upgrade-quota')
                ->with('error', 'No active Premium plan was found.');
        }

        $subscription->update(['status' => 'cancelled']);

        return redirect()
            ->route('user.upgrade-quota')
            ->with('success', 'Your Premium plan has been cancelled. Premium access remains active until the end of your billing period.');
    }
}
