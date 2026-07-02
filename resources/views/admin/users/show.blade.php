@extends('layouts.admin.app')

@section('title', 'User Detail — ' . $user->name . ' - Admin')

@section('content')
<div class="admin-shell">

    <!-- Breadcrumb + Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-sm font-label text-primary/50 mb-2">
                <a href="{{ route('admin.users') }}" class="hover:text-primary transition-colors">User Management</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary">{{ $user->name }}</span>
            </div>
            <h1 class="text-3xl font-headline font-bold text-primary">User Detail</h1>
        </div>
        <a href="{{ route('admin.users') }}"
           class="admin-btn-secondary">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Back to Users</span>
        </a>
    </div>

    <!-- Profile Card + Quick Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Profile Card -->
        <div class="lg:col-span-2 admin-card-pad p-8">
            <div class="flex items-start space-x-6">
                <div class="w-20 h-20 rounded-lg overflow-hidden bg-primary/10 flex-shrink-0">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                         class="w-full h-full object-cover"
                         onerror="this.outerHTML='<div class=\'w-full h-full flex items-center justify-center text-2xl font-bold text-primary/50\'>{{ strtoupper(substr($user->name, 0, 2)) }}</div>'">
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center space-x-3 mb-1">
                        <h2 class="text-2xl font-headline font-bold text-primary truncate">{{ $user->name }}</h2>
                        @if($user->is_suspended)
                            <span class="admin-badge bg-red-50 text-red-600">Suspended</span>
                        @else
                            <span class="admin-badge bg-secondary/10 text-secondary">Active</span>
                        @endif
                    </div>
                    <p class="text-sm font-label text-primary/60 mb-4">{{ $user->email }}</p>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-1">Plan</p>
                            @if($user->role === 'premium')
                                <span class="admin-badge bg-amber-100 text-amber-700">
                                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1">star</span>
                                    <span>Premium</span>
                                </span>
                            @else
                                <span class="admin-badge bg-primary/10 text-primary/60">Free</span>
                            @endif
                        </div>
                        <div>
                            <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-1">Joined</p>
                            <p class="text-sm font-label text-primary">{{ $user->created_at->format('d M Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-1">Last Login</p>
                            <p class="text-sm font-label text-primary">
                                {{ $user->updated_at ? $user->updated_at->diffForHumans() : 'Never' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-1">Email Verified</p>
                            <p class="text-sm font-label {{ $user->email_verified_at ? 'text-secondary' : 'text-red-500' }}">
                                {{ $user->email_verified_at ? $user->email_verified_at->format('d M Y') : 'Not verified' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="admin-card-pad space-y-3" x-data="userActions()">

            <h3 class="admin-section-title mb-4">Quick Actions</h3>

            <!-- Override Plan -->
            <button type="button" @click="openPlanModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->role }}')"
                    class="flex min-h-11 w-full items-center space-x-3 rounded-lg border border-primary/10 px-4 py-3 text-left transition-colors hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">
                <span class="material-symbols-outlined text-primary/50 text-[20px]">card_membership</span>
                <span class="text-sm font-label text-primary">Override Plan</span>
            </button>

            <!-- Adjust Credits -->
            <button type="button" @click="openCreditsModal('{{ $user->id }}', '{{ $user->name }}', {{ $user->ai_quota_used }})"
                    class="flex min-h-11 w-full items-center space-x-3 rounded-lg border border-primary/10 px-4 py-3 text-left transition-colors hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">
                <span class="material-symbols-outlined text-primary/50 text-[20px]">toll</span>
                <span class="text-sm font-label text-primary">Adjust AI Credits</span>
            </button>

            <!-- Suspend / Activate -->
            <button type="button" @click="openSuspendModal('{{ $user->id }}', '{{ $user->name }}', {{ $user->is_suspended ? 'true' : 'false' }})"
                    class="flex min-h-11 w-full items-center space-x-3 rounded-lg border border-primary/10 px-4 py-3 text-left transition-colors hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40">
                <span class="material-symbols-outlined text-[20px] {{ $user->is_suspended ? 'text-secondary' : 'text-amber-500' }}">
                    {{ $user->is_suspended ? 'lock_open' : 'block' }}
                </span>
                <span class="text-sm font-label text-primary">{{ $user->is_suspended ? 'Activate Account' : 'Suspend Account' }}</span>
            </button>

            <!-- Delete -->
            <button type="button" @click="openDeleteModal('{{ $user->id }}', '{{ $user->name }}')"
                    class="flex min-h-11 w-full items-center space-x-3 rounded-lg border border-red-100 px-4 py-3 text-left transition-colors hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-300">
                <span class="material-symbols-outlined text-red-400 text-[20px]">delete_forever</span>
                <span class="text-sm font-label text-red-600">Delete Account</span>
            </button>

            <x-ui.modal id="admin-user-show-plan-modal" title="Override Plan" description="Change plan access for this user.">
                    <form :action="planModal.url" method="POST">
                        @csrf @method('PATCH')
                        <div class="mb-6">
                            <p class="mb-4 text-sm text-primary/60">Changing plan for <strong x-text="planModal.name"></strong></p>
                            <label for="admin-user-plan" class="admin-section-title mb-2 block">New Plan</label>
                            <select id="admin-user-plan" name="plan" class="admin-filter-field w-full">
                                <option value="basic" :selected="planModal.current === 'basic'">Free (Basic)</option>
                                <option value="premium" :selected="planModal.current === 'premium'">Premium</option>
                            </select>
                        </div>
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" class="admin-btn-secondary" x-on:click="closeModal()">Cancel</button>
                            <x-ui.loading-button loading-text="Applying..." icon="workspace_premium">Apply</x-ui.loading-button>
                        </div>
                    </form>
            </x-ui.modal>

            <x-ui.modal id="admin-user-show-credits-modal" title="Adjust AI Credits" description="Set this user's consumed AI credit count.">
                    <form :action="creditsModal.url" method="POST">
                        @csrf @method('PATCH')
                        <div class="mb-6">
                            <p class="mb-4 text-sm text-primary/60">Set used credits for <strong x-text="creditsModal.name"></strong></p>
                            <label for="admin-user-ai-quota-used" class="admin-section-title mb-2 block">AI Quota Used</label>
                            <input id="admin-user-ai-quota-used" type="number" name="ai_quota_used" :value="creditsModal.current" min="0"
                                   class="admin-filter-field w-full">
                        </div>
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" class="admin-btn-secondary" x-on:click="closeModal()">Cancel</button>
                            <x-ui.loading-button loading-text="Updating..." icon="token">Update</x-ui.loading-button>
                        </div>
                    </form>
            </x-ui.modal>

            <x-ui.modal id="admin-user-show-suspend-modal" title="Confirm Account Access" description="Review this account status change before applying it.">
                    <p class="mb-6 text-sm font-label text-primary/60">
                        <span x-text="suspendModal.isSuspended ? 'Restore access for' : 'Block access for'"></span>
                        <strong x-text="suspendModal.name"></strong>?
                    </p>
                    <form :action="suspendModal.url" method="POST">
                        @csrf @method('PATCH')
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" class="admin-btn-secondary" x-on:click="closeModal()">Cancel</button>
                            <x-ui.loading-button x-show="suspendModal.isSuspended" variant="secondary" loading-text="Activating..." icon="lock_open">Activate</x-ui.loading-button>
                            <x-ui.loading-button x-show="!suspendModal.isSuspended" variant="danger" loading-text="Suspending..." icon="block">Suspend</x-ui.loading-button>
                        </div>
                    </form>
            </x-ui.modal>

            <x-ui.modal id="admin-user-show-delete-modal" title="Delete Account" description="This destructive action cannot be undone.">
                    <x-ui.alert variant="error" title="Permanent deletion" class="mb-6">
                        Permanently delete <strong x-text="deleteModal.name"></strong>? This cannot be undone.
                    </x-ui.alert>
                    <form :action="deleteModal.url" method="POST">
                        @csrf @method('DELETE')
                        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button type="button" class="admin-btn-secondary" x-on:click="closeModal()">Cancel</button>
                            <x-ui.loading-button variant="danger" loading-text="Deleting..." icon="delete_forever">Delete</x-ui.loading-button>
                        </div>
                    </form>
            </x-ui.modal>
        </div>
    </div>

    <!-- AI Quota -->
    <div class="admin-card-pad">
        <h3 class="admin-section-title mb-5">AI Quota Usage</h3>
        @php
            $limit = $user->getQuotaLimit();
            $used  = $user->ai_quota_used;
            $pct   = $limit > 0 ? min(100, round($used / $limit * 100)) : 0;
        @endphp
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-label text-primary">{{ number_format($used) }} / {{ number_format($limit) }} credits used</span>
            <span class="text-xs font-label {{ $pct >= 90 ? 'text-red-500' : 'text-primary/50' }}">{{ $pct }}%</span>
        </div>
        <div class="h-2.5 bg-primary/5 rounded-full overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500
                        {{ $pct >= 90 ? 'bg-red-400' : ($pct >= 70 ? 'bg-amber-400' : 'bg-secondary') }}"
                 style="width: {{ $pct }}%"></div>
        </div>
        <div class="mt-4 grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-xl font-headline font-bold text-primary">{{ number_format($totalAiActions) }}</p>
                <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mt-1">Total Actions</p>
            </div>
            <div>
                <p class="text-xl font-headline font-bold text-primary">${{ number_format($totalAiSpend, 4) }}</p>
                <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mt-1">Total Spend</p>
            </div>
            <div>
                <p class="text-xl font-headline font-bold text-primary">{{ number_format($user->getQuotaRemaining()) }}</p>
                <p class="text-[10px] font-label text-primary/50 uppercase tracking-widest mt-1">Remaining</p>
            </div>
        </div>
    </div>

    <!-- Three-column bottom section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Resumes -->
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-5">Resumes ({{ $user->cvs->count() }} recent)</h3>
            @forelse($user->cvs as $cv)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-primary/5' : '' }}">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-label text-primary truncate">{{ $cv->title ?? 'Untitled Resume' }}</p>
                        <p class="text-[11px] font-label text-primary/40 mt-0.5">{{ $cv->created_at->format('d M Y') }}</p>
                    </div>
                    <span class="material-symbols-outlined text-primary/20 text-[18px] ml-2">description</span>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <span class="material-symbols-outlined text-primary/20 text-[40px] mb-2">description</span>
                    <p class="text-sm font-label text-primary/40">No resumes yet</p>
                </div>
            @endforelse
        </div>

        <!-- Subscription -->
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-5">Subscription</h3>
            @if($subscription)
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-label text-primary/60">Status</span>
                        <span class="text-sm font-label {{ $subscription->status === 'active' ? 'text-secondary' : 'text-primary/60' }} capitalize">
                            {{ $subscription->status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-label text-primary/60">Plan</span>
                        <span class="text-sm font-label text-primary capitalize">{{ $subscription->plan }}</span>
                    </div>
                    @if($subscription->starts_at)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-label text-primary/60">Started</span>
                        <span class="text-sm font-label text-primary">{{ $subscription->starts_at->format('d M Y') }}</span>
                    </div>
                    @endif
                    @if($subscription->ends_at)
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-label text-primary/60">Expires</span>
                        <span class="text-sm font-label {{ $subscription->ends_at->isPast() ? 'text-red-500' : 'text-primary' }}">
                            {{ $subscription->ends_at->format('d M Y') }}
                        </span>
                    </div>
                    @endif
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <span class="material-symbols-outlined text-primary/20 text-[40px] mb-2">card_membership</span>
                    <p class="text-sm font-label text-primary/40">No subscription record</p>
                    <p class="text-xs font-label text-primary/30 mt-1">Free plan</p>
                </div>
            @endif
        </div>

        <!-- Recent Transactions -->
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-5">Transactions</h3>
            @forelse($transactions as $tx)
                <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-primary/5' : '' }}">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-label text-primary">Rp {{ number_format($tx->amount, 0, ',', '.') }}</p>
                        <p class="text-[11px] font-label text-primary/40 mt-0.5">{{ $tx->created_at->format('d M Y') }}</p>
                    </div>
                    @php
                        $statusMap = [
                            'success'  => 'bg-secondary/10 text-secondary',
                            'pending'  => 'bg-amber-100 text-amber-600',
                            'failed'   => 'bg-red-100 text-red-500',
                            'expired'  => 'bg-primary/10 text-primary/40',
                        ];
                    @endphp
                    <span class="text-[10px] font-label px-2.5 py-1 rounded-full {{ $statusMap[$tx->status] ?? 'bg-primary/10 text-primary/40' }} capitalize ml-2">
                        {{ $tx->status }}
                    </span>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-8 text-center">
                    <span class="material-symbols-outlined text-primary/20 text-[40px] mb-2">receipt_long</span>
                    <p class="text-sm font-label text-primary/40">No transactions</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent AI Usage Logs -->
    @if($user->aiUsageLogs->isNotEmpty())
    <div class="admin-card-pad">
        <h3 class="admin-section-title mb-5">Recent AI Usage (last 10)</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-primary/5">
                        <th class="text-left text-[10px] font-label text-primary/40 uppercase tracking-widest pb-3 pr-4">Action</th>
                        <th class="text-left text-[10px] font-label text-primary/40 uppercase tracking-widest pb-3 pr-4">Tokens</th>
                        <th class="text-left text-[10px] font-label text-primary/40 uppercase tracking-widest pb-3 pr-4">Cost</th>
                        <th class="text-left text-[10px] font-label text-primary/40 uppercase tracking-widest pb-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @foreach($user->aiUsageLogs as $log)
                    <tr class="hover:bg-primary/[0.02] transition-colors">
                        <td class="py-3 pr-4">
                            <span class="text-sm font-label text-primary capitalize">{{ str_replace('_', ' ', $log->action_type) }}</span>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="text-sm font-label text-primary/70">{{ number_format($log->tokens_used) }}</span>
                        </td>
                        <td class="py-3 pr-4">
                            <span class="text-sm font-label text-primary/70">${{ number_format($log->cost_usd, 4) }}</span>
                        </td>
                        <td class="py-3">
                            <span class="text-sm font-label text-primary/50">{{ $log->created_at->format('d M Y, H:i') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function userActions() {
    return {
        planModal:    { open: false, url: '', name: '', current: '' },
        creditsModal: { open: false, url: '', name: '', current: 0 },
        suspendModal: { open: false, url: '', name: '', isSuspended: false },
        deleteModal:  { open: false, url: '', name: '' },

        openPlanModal(id, name, current) {
            this.planModal = { open: true, url: `/admin/users/${id}/plan`, name, current };
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'admin-user-show-plan-modal' }));
        },
        openCreditsModal(id, name, current) {
            this.creditsModal = { open: true, url: `/admin/users/${id}/credits`, name, current };
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'admin-user-show-credits-modal' }));
        },
        openSuspendModal(id, name, isSuspended) {
            this.suspendModal = { open: true, url: `/admin/users/${id}/suspend`, name, isSuspended };
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'admin-user-show-suspend-modal' }));
        },
        openDeleteModal(id, name) {
            this.deleteModal = { open: true, url: `/admin/users/${id}`, name };
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'admin-user-show-delete-modal' }));
        },
    };
}
</script>
@endpush
