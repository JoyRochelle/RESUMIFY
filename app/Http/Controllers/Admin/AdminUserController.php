<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\AdjustUserCreditsAction;
use App\Actions\Admin\DeleteUserAction;
use App\Actions\Admin\OverrideUserPlanAction;
use App\Actions\Admin\ToggleUserSuspensionAction;
use App\Http\Controllers\Controller;
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

    public function overridePlan(Request $request, User $user, OverrideUserPlanAction $overrideUserPlan): RedirectResponse
    {
        abort_if($user->isAdmin(), 403);

        $request->validate(['plan' => 'required|in:basic,premium']);

        $newRole = $request->plan;
        $overrideUserPlan->execute(auth()->user(), $user, $newRole);

        return back()->with('success', "Plan updated to {$newRole} for {$user->name}.");
    }

    public function adjustCredits(Request $request, User $user, AdjustUserCreditsAction $adjustUserCredits): RedirectResponse
    {
        abort_if($user->isAdmin(), 403);

        $request->validate([
            'ai_quota_used' => 'required|integer|min:0',
        ]);

        $adjustUserCredits->execute(auth()->user(), $user, (int) $request->ai_quota_used);

        return back()->with('success', "AI credits adjusted for {$user->name}.");
    }

    public function toggleSuspend(User $user, ToggleUserSuspensionAction $toggleUserSuspension): RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Admin accounts cannot be suspended.');

        $isSuspended = $toggleUserSuspension->execute(auth()->user(), $user);
        $action = $isSuspended ? 'suspended' : 'activated';
        return back()->with('success', "{$user->name} has been {$action}.");
    }

    public function destroy(User $user, DeleteUserAction $deleteUser): RedirectResponse
    {
        abort_if($user->isAdmin(), 403, 'Admin accounts cannot be deleted.');
        abort_if($user->id === auth()->id(), 403, 'You cannot delete your own account.');

        $name = $user->name;
        $deleteUser->execute(auth()->user(), $user);

        return redirect()->route('admin.users')
            ->with('success', "{$name} has been permanently deleted.");
    }
}
