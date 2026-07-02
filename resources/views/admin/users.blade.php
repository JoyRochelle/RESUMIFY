@extends('layouts.admin.app')

@section('title', 'User Management - Admin Dashboard')

@section('content')
<div class="admin-shell" x-data="userMgmt()">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-headline font-bold text-primary mb-2">User Management</h1>
            <p class="text-sm font-label text-primary/60">Manage access, subscriptions, and AI quotas.</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-4">TOTAL USERS</h3>
            <div class="flex items-end">
                <span class="text-3xl font-headline text-primary mr-3">{{ number_format($totalUsers) }}</span>
            </div>
        </div>
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-4">PREMIUM USERS</h3>
            <div class="flex items-end">
                <span class="text-3xl font-headline text-primary mr-3">{{ number_format($premiumUsers) }}</span>
                @if($totalUsers > 0)
                    <span class="text-xs font-label text-primary/40 mb-1">{{ round($premiumUsers / $totalUsers * 100) }}% of total</span>
                @endif
            </div>
        </div>
        <div class="admin-card-pad">
            <h3 class="admin-section-title mb-4">NEW (TODAY)</h3>
            <div class="flex items-end">
                <span class="text-3xl font-headline text-secondary mr-3">+{{ $newToday }}</span>
                <span class="text-xs font-label text-primary/40 mb-1">registrations today</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex h-12 min-w-[200px] flex-1 items-center rounded-lg border border-primary/10 bg-tertiary px-4 shadow-sm transition-colors focus-within:ring-2 focus-within:ring-secondary/30">
            <span class="material-symbols-outlined text-primary/40 mr-3 text-[20px]">search</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name or email..."
                   class="bg-transparent border-none focus:outline-none text-sm font-label w-full text-primary placeholder:text-primary/40">
        </div>

        <select name="plan"
                class="admin-filter-field h-12 cursor-pointer">
            <option value="">All Plans</option>
            <option value="basic"   {{ request('plan') === 'basic'   ? 'selected' : '' }}>Free</option>
            <option value="premium" {{ request('plan') === 'premium' ? 'selected' : '' }}>Premium</option>
        </select>

        <select name="status"
                class="admin-filter-field h-12 cursor-pointer">
            <option value="">All Status</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>

        <x-ui.loading-button loading-text="Filtering..." icon="filter_list">Filter</x-ui.loading-button>

        @if(request()->hasAny(['search','plan','status']))
            <a href="{{ route('admin.users') }}"
               class="text-sm font-label text-primary/50 hover:text-primary transition">
                Clear
            </a>
        @endif
    </form>

    <!-- User Table -->
    <div class="admin-card overflow-hidden">

        {{-- Mobile card layout --}}
        <div class="md:hidden divide-y divide-primary/5">
            @forelse($users as $user)
                @php
                    $quota    = $user->getQuotaLimit();
                    $used     = $user->ai_quota_used ?? 0;
                    $initials = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
                @endphp
                <div class="p-4 flex items-center gap-3 {{ $user->is_suspended ? 'opacity-60' : '' }}">
                    <div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-primary font-bold text-sm shrink-0">
                        {{ $initials }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <a href="{{ route('admin.users.show', $user) }}"
                           class="text-sm font-bold text-primary hover:text-secondary transition truncate block">
                            {{ $user->name }}
                        </a>
                        <p class="text-xs text-primary/40 truncate">{{ $user->email }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if($user->role === 'premium')
                            <span class="admin-badge bg-secondary/10 text-secondary">PRO</span>
                        @else
                            <span class="admin-badge border border-primary/10 bg-surface-container-low text-primary/60">FREE</span>
                        @endif
                        @if($user->is_suspended)
                            <span class="w-2 h-2 rounded-full bg-red-400 inline-block" title="Suspended"></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-secondary inline-block" title="Active"></span>
                        @endif
                        <a href="{{ route('admin.users.show', $user) }}"
                           class="admin-icon-action"
                           aria-label="View {{ $user->name }}">
                            <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                        </a>
                    </div>
                </div>
            @empty
                <x-ui.empty-state title="No users found" description="Try changing the search, plan, or status filter." icon="group" class="m-4" />
            @endforelse
        </div>

        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
        <table class="admin-table">
            <thead class="admin-table-head">
                <tr>
                    <th class="admin-th">USER</th>
                    <th class="admin-th">PLAN</th>
                    <th class="admin-th">AI CREDIT</th>
                    <th class="admin-th">JOINED</th>
                    <th class="admin-th">STATUS</th>
                    <th class="admin-th text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/10">
                @forelse($users as $user)
                    @php
                        $quota     = $user->getQuotaLimit();
                        $used      = $user->ai_quota_used ?? 0;
                        $pct       = $quota > 0 ? min(100, round($used / $quota * 100)) : 0;
                        $initials  = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
                    @endphp
                    <tr class="hover:bg-surface/50 transition {{ $user->is_suspended ? 'opacity-60' : '' }}">
                        <!-- User -->
                        <td class="admin-td">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-surface-container-low flex items-center justify-center text-primary font-bold text-sm shrink-0">
                                    {{ $initials }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="text-sm font-label font-bold text-primary hover:text-secondary transition">
                                        {{ $user->name }}
                                    </a>
                                    <p class="text-xs font-label text-primary/40">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>

                        <!-- Plan -->
                        <td class="admin-td">
                            @if($user->role === 'premium')
                                <span class="admin-badge bg-secondary/10 text-secondary">PREMIUM</span>
                            @else
                                <span class="admin-badge border border-primary/10 bg-surface-container-low text-primary/60">FREE</span>
                            @endif
                        </td>

                        <!-- AI Credit -->
                        <td class="admin-td w-44">
                            <div class="flex justify-between text-[10px] font-label mb-1">
                                <span class="text-primary font-bold">{{ $used }}/{{ $quota }}</span>
                                <span class="text-primary/40">{{ $pct }}%</span>
                            </div>
                            <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden">
                                <div class="bg-primary h-full rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </td>

                        <!-- Joined -->
                        <td class="admin-td font-headline italic text-primary/60">
                            {{ $user->created_at->format('d M Y') }}
                        </td>

                        <!-- Status -->
                        <td class="admin-td">
                            @if($user->is_suspended)
                                <span class="admin-badge bg-red-50 text-red-600"><span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>Suspended</span>
                            @else
                                <span class="admin-badge bg-secondary/10 text-secondary"><span class="h-1.5 w-1.5 rounded-full bg-secondary"></span>Active</span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="admin-td text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <!-- View -->
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="admin-icon-action"
                                   title="View detail"
                                   aria-label="View {{ $user->name }}">
                                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">open_in_new</span>
                                </a>

                                <!-- Override Plan -->
                                <button type="button" @click="openPlanModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->role }}')"
                                        class="admin-icon-action hover:text-secondary"
                                        title="Override plan"
                                        aria-label="Override plan for {{ $user->name }}">
                                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">workspace_premium</span>
                                </button>

                                <!-- Adjust Credits -->
                                <button type="button" @click="openCreditsModal('{{ $user->id }}', '{{ $user->name }}', {{ $used }})"
                                        class="admin-icon-action"
                                        title="Adjust credits"
                                        aria-label="Adjust credits for {{ $user->name }}">
                                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">token</span>
                                </button>

                                <!-- Suspend / Activate -->
                                <button type="button" @click="openSuspendModal('{{ $user->id }}', '{{ $user->name }}', {{ $user->is_suspended ? 'true' : 'false' }})"
                                        class="admin-icon-action {{ $user->is_suspended ? 'text-secondary' : 'text-amber-600' }}"
                                        title="{{ $user->is_suspended ? 'Activate' : 'Suspend' }}"
                                        aria-label="{{ $user->is_suspended ? 'Activate' : 'Suspend' }} {{ $user->name }}">
                                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">{{ $user->is_suspended ? 'lock_open' : 'block' }}</span>
                                </button>

                                <!-- Delete -->
                                <button type="button" @click="openDeleteModal('{{ $user->id }}', '{{ $user->name }}')"
                                        class="admin-icon-action text-red-500 hover:bg-red-50 hover:text-red-600"
                                        title="Delete permanently"
                                        aria-label="Delete {{ $user->name }}">
                                    <span class="material-symbols-outlined text-[18px]" aria-hidden="true">delete_forever</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4">
                            <x-ui.empty-state title="No users found" description="Try changing the search, plan, or status filter." icon="group" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>{{-- end desktop table --}}

        <!-- Pagination -->
        <div class="flex flex-col gap-4 border-t border-primary/10 p-6 text-sm font-label sm:flex-row sm:items-center sm:justify-between">
            <div class="text-primary/60">
                Showing <span class="font-bold text-primary">{{ $users->firstItem() ?? 0 }}</span>–<span class="font-bold text-primary">{{ $users->lastItem() ?? 0 }}</span>
                of <span class="font-bold text-primary">{{ number_format($users->total()) }}</span> users
            </div>
            <div>{{ $users->links() }}</div>
        </div>
    </div>

    <x-ui.modal id="admin-users-plan-modal" title="Override Plan" description="Change plan access for this user.">
            <form :action="`/admin/users/${planModal.userId}/plan`" method="POST">
                @csrf @method('PATCH')
                <p class="mb-4 text-sm text-primary/60">
                    Updating <span class="font-bold text-primary" x-text="planModal.name"></span>.
                </p>
                <label for="admin-users-plan" class="admin-section-title mb-2 block">New plan</label>
                <select id="admin-users-plan" name="plan" x-model="planModal.currentRole"
                        class="admin-filter-field mb-6 w-full">
                    <option value="basic">Free (Basic)</option>
                    <option value="premium">Premium</option>
                </select>
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="admin-btn-secondary" x-on:click="closeModal()">Cancel</button>
                    <x-ui.loading-button loading-text="Updating..." icon="workspace_premium">Update Plan</x-ui.loading-button>
                </div>
            </form>
    </x-ui.modal>

    <x-ui.modal id="admin-users-credits-modal" title="Adjust AI Credits Used" description="Set this user's consumed AI credit count.">
            <p class="mb-4 text-sm text-primary/60">
                Set <span class="font-bold text-primary" x-text="creditsModal.name"></span>'s consumed credit count.
            </p>
            <form :action="`/admin/users/${creditsModal.userId}/credits`" method="POST">
                @csrf @method('PATCH')
                <label for="admin-users-ai-quota-used" class="admin-section-title mb-2 block">AI quota used</label>
                <input id="admin-users-ai-quota-used" type="number" name="ai_quota_used" :value="creditsModal.used" min="0"
                       class="admin-filter-field mb-6 w-full">
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="admin-btn-secondary" x-on:click="closeModal()">Cancel</button>
                    <x-ui.loading-button loading-text="Saving..." icon="token">Save</x-ui.loading-button>
                </div>
            </form>
    </x-ui.modal>

    <x-ui.modal id="admin-users-suspend-modal" title="Confirm Account Access" description="Review this account status change before applying it.">
            <p class="mb-6 text-sm font-label text-primary/60">
                <span x-text="suspendModal.isSuspended
                    ? `Activate ${suspendModal.name}? They will be able to log in again.`
                    : `Suspend ${suspendModal.name}? They will be logged out immediately.`">
                </span>
            </p>
            <form :action="`/admin/users/${suspendModal.userId}/suspend`" method="POST">
                @csrf @method('PATCH')
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="admin-btn-secondary" x-on:click="closeModal()">Cancel</button>
                    <x-ui.loading-button x-show="suspendModal.isSuspended" variant="secondary" loading-text="Activating..." icon="lock_open">Activate</x-ui.loading-button>
                    <x-ui.loading-button x-show="!suspendModal.isSuspended" variant="danger" loading-text="Suspending..." icon="block">Suspend</x-ui.loading-button>
                </div>
            </form>
    </x-ui.modal>

    <x-ui.modal id="admin-users-delete-modal" title="Delete Permanently" description="This destructive action cannot be undone.">
            <x-ui.alert variant="error" title="Permanent deletion" class="mb-6">
                This will permanently delete <span class="font-bold text-primary" x-text="deleteModal.name"></span>
                and all their data. This action cannot be undone.
            </x-ui.alert>
            <form :action="`/admin/users/${deleteModal.userId}`" method="POST">
                @csrf @method('DELETE')
                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" class="admin-btn-secondary" x-on:click="closeModal()">Cancel</button>
                    <x-ui.loading-button variant="danger" loading-text="Deleting..." icon="delete_forever">Delete</x-ui.loading-button>
                </div>
            </form>
    </x-ui.modal>

</div>
@endsection

@push('scripts')
<script>
function userMgmt() {
    return {
        planModal:    { open: false, userId: '', name: '', currentRole: 'basic' },
        creditsModal: { open: false, userId: '', name: '', used: 0 },
        suspendModal: { open: false, userId: '', name: '', isSuspended: false },
        deleteModal:  { open: false, userId: '', name: '' },

        openPlanModal(id, name, role) {
            this.planModal = { open: true, userId: id, name, currentRole: role };
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'admin-users-plan-modal' }));
        },
        openCreditsModal(id, name, used) {
            this.creditsModal = { open: true, userId: id, name, used };
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'admin-users-credits-modal' }));
        },
        openSuspendModal(id, name, isSuspended) {
            this.suspendModal = { open: true, userId: id, name, isSuspended };
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'admin-users-suspend-modal' }));
        },
        openDeleteModal(id, name) {
            this.deleteModal = { open: true, userId: id, name };
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'admin-users-delete-modal' }));
        },
    };
}
</script>
@endpush
