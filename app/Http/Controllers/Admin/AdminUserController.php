<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminLog;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->whereIn('role', ['basic', 'premium'])
            ->when($request->search, fn($q, $s) =>
                $q->where(fn($q2) =>
                    $q2->where('name', 'like', "%{$s}%")
                       ->orWhere('email', 'like', "%{$s}%")
                )
            )
            ->when($request->plan, fn($q, $p) => $q->where('role', $p))
            ->when($request->filled('status'), fn($q) =>
                $q->where('is_suspended', $request->status === 'suspended')
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $totalUsers   = User::whereIn('role', ['basic', 'premium'])->count();
        $premiumUsers = User::where('role', 'premium')->count();
        $newToday     = User::whereIn('role', ['basic', 'premium'])
                            ->whereDate('created_at', today())
                            ->count();

        return view('admin.users', compact(
            'users', 'totalUsers', 'premiumUsers', 'newToday'
        ));
    }

    public function show(User $user): View
    {
        abort_if($user->isAdmin(), 403);

        $user->load([
            'cvs'         => fn($q) => $q->latest()->limit(5),
            'aiUsageLogs' => fn($q) => $q->latest()->limit(10),
        ]);

        $transactions   = $user->transactions()->latest()->limit(5)->get();
        $subscription   = $user->subscriptions()->latest()->first();
        $totalAiSpend   = $user->aiUsageLogs()->sum('cost_usd');
        $totalAiActions = $user->aiUsageLogs()->count();

        return view('admin.users.show', compact(
            'user', 'transactions', 'subscription', 'totalAiSpend', 'totalAiActions'
        ));
    }

    public function overridePlan(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403);

        $request->validate(['plan' => 'required|in:basic,premium']);

        $oldRole = $user->role;
        $newRole = $request->plan;

        $user->update(['role' => $newRole]);

        if ($newRole === 'premium') {
            Subscription::create([
                'user_id'   => $user->id,
                'plan'      => 'premium',
                'status'    => 'active',
                'starts_at' => now(),
                'ends_at'   => now()->addMonth(),
            ]);
            $user->update(['ai_quota_used' => 0]);
        }

        AdminLog::create([
            'admin_id'    => auth()->id(),
            'action'      => "override_plan:{$oldRole}→{$newRole}",
            'target_type' => 'user',
            'target_id'   => $user->id,
        ]);

        return back()->with('success', "Plan updated to {$newRole} for {$user->name}.");
    }

    public function adjustCredits(Request $request, User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403);

        $request->validate([
            'ai_quota_used' => 'required|integer|min:0',
        ]);

        $user->update(['ai_quota_used' => $request->ai_quota_used]);

        AdminLog::create([
            'admin_id'    => auth()->id(),
            'action'      => "adjust_credits:{$request->ai_quota_used}",
            'target_type' => 'user',
            'target_id'   => $user->id,
        ]);

        return back()->with('success', "AI credits adjusted for {$user->name}.");
    }

    public function toggleSuspend(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Admin accounts cannot be suspended.');

        $wasSuspended = $user->is_suspended;
        $user->update(['is_suspended' => ! $wasSuspended]);

        AdminLog::create([
            'admin_id'    => auth()->id(),
            'action'      => $wasSuspended ? 'activate_user' : 'suspend_user',
            'target_type' => 'user',
            'target_id'   => $user->id,
        ]);

        $action = $wasSuspended ? 'activated' : 'suspended';
        return back()->with('success', "{$user->name} has been {$action}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Admin accounts cannot be deleted.');
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');

        $name = $user->name;

        AdminLog::create([
            'admin_id'    => auth()->id(),
            'action'      => 'hard_delete_user',
            'target_type' => 'user',
            'target_id'   => $user->id,
        ]);

        $user->forceDelete();

        return redirect()->route('admin.users')
            ->with('success', "{$name} has been permanently deleted.");
    }
}
