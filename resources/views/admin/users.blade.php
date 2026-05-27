@extends('layouts.admin.app')

@section('title', 'User Management - Admin Dashboard')

@section('content')
<div class="max-w-6xl mx-auto space-y-8 pb-10" x-data="userMgmt()">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-headline font-bold text-primary mb-2">User Management</h1>
            <p class="text-sm font-label text-primary/60">Manage access, subscriptions, and AI quotas.</p>
        </div>
    </div>

    <!-- Flash messages -->
    @if(session('success'))
        <div class="bg-secondary/10 border border-secondary/20 text-secondary text-sm font-label px-5 py-3 rounded-xl flex items-center space-x-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">TOTAL USERS</h3>
            <div class="flex items-end">
                <span class="text-3xl font-headline text-primary mr-3">{{ number_format($totalUsers) }}</span>
            </div>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">PREMIUM USERS</h3>
            <div class="flex items-end">
                <span class="text-3xl font-headline text-primary mr-3">{{ number_format($premiumUsers) }}</span>
                @if($totalUsers > 0)
                    <span class="text-xs font-label text-primary/40 mb-1">{{ round($premiumUsers / $totalUsers * 100) }}% of total</span>
                @endif
            </div>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
            <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">NEW (TODAY)</h3>
            <div class="flex items-end">
                <span class="text-3xl font-headline text-secondary mr-3">+{{ $newToday }}</span>
                <span class="text-xs font-label text-primary/40 mb-1">registrations today</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <form method="GET" action="{{ route('admin.users') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px] bg-white rounded-xl border border-primary/10 flex items-center px-4 h-12 shadow-sm focus-within:border-primary/30 transition-colors">
            <span class="material-symbols-outlined text-primary/40 mr-3 text-[20px]">search</span>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Search by name or email..."
                   class="bg-transparent border-none focus:outline-none text-sm font-label w-full text-primary placeholder:text-primary/40">
        </div>

        <select name="plan"
                class="bg-white rounded-xl border border-primary/10 px-4 h-12 text-sm font-label text-primary shadow-sm focus:outline-none focus:border-primary/30 cursor-pointer">
            <option value="">All Plans</option>
            <option value="basic"   {{ request('plan') === 'basic'   ? 'selected' : '' }}>Free</option>
            <option value="premium" {{ request('plan') === 'premium' ? 'selected' : '' }}>Premium</option>
        </select>

        <select name="status"
                class="bg-white rounded-xl border border-primary/10 px-4 h-12 text-sm font-label text-primary shadow-sm focus:outline-none focus:border-primary/30 cursor-pointer">
            <option value="">All Status</option>
            <option value="active"    {{ request('status') === 'active'    ? 'selected' : '' }}>Active</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>

        <button type="submit"
                class="bg-primary text-white px-6 h-12 rounded-xl text-sm font-label hover:bg-primary/90 transition shadow-sm">
            Filter
        </button>

        @if(request()->hasAny(['search','plan','status']))
            <a href="{{ route('admin.users') }}"
               class="text-sm font-label text-primary/50 hover:text-primary transition">
                Clear
            </a>
        @endif
    </form>

    <!-- User Table -->
    <div class="bg-white rounded-3xl shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-primary/5">
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest">USER</th>
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest">PLAN</th>
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest">AI CREDIT</th>
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest">JOINED</th>
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest">STATUS</th>
                    <th class="py-4 px-6 text-[10px] font-label text-primary/60 uppercase tracking-widest text-right">ACTIONS</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/5">
                @forelse($users as $user)
                    @php
                        $quota     = $user->getQuotaLimit();
                        $used      = $user->ai_quota_used ?? 0;
                        $pct       = $quota > 0 ? min(100, round($used / $quota * 100)) : 0;
                        $initials  = collect(explode(' ', $user->name))->take(2)->map(fn($w) => strtoupper($w[0] ?? ''))->implode('');
                    @endphp
                    <tr class="hover:bg-surface/50 transition {{ $user->is_suspended ? 'opacity-60' : '' }}">
                        <!-- User -->
                        <td class="py-4 px-6">
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
                        <td class="py-4 px-6">
                            @if($user->role === 'premium')
                                <span class="bg-secondary text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">PREMIUM</span>
                            @else
                                <span class="bg-surface-container-low text-primary/60 border border-primary/10 text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider">FREE</span>
                            @endif
                        </td>

                        <!-- AI Credit -->
                        <td class="py-4 px-6 w-44">
                            <div class="flex justify-between text-[10px] font-label mb-1">
                                <span class="text-primary font-bold">{{ $used }}/{{ $quota }}</span>
                                <span class="text-primary/40">{{ $pct }}%</span>
                            </div>
                            <div class="w-full bg-surface-container-low h-1.5 rounded-full overflow-hidden">
                                <div class="bg-primary h-full rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </td>

                        <!-- Joined -->
                        <td class="py-4 px-6 text-sm font-headline italic text-primary/60">
                            {{ $user->created_at->format('d M Y') }}
                        </td>

                        <!-- Status -->
                        <td class="py-4 px-6">
                            @if($user->is_suspended)
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 rounded-full bg-red-400"></div>
                                    <span class="text-xs font-label text-red-400">Suspended</span>
                                </div>
                            @else
                                <div class="flex items-center space-x-2">
                                    <div class="w-2 h-2 rounded-full bg-secondary"></div>
                                    <span class="text-xs font-label text-primary">Active</span>
                                </div>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end space-x-1">
                                <!-- View -->
                                <a href="{{ route('admin.users.show', $user) }}"
                                   class="w-8 h-8 flex items-center justify-center rounded-lg text-primary/40 hover:text-primary hover:bg-surface transition"
                                   title="View detail">
                                    <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                                </a>

                                <!-- Override Plan -->
                                <button @click="openPlanModal('{{ $user->id }}', '{{ $user->name }}', '{{ $user->role }}')"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-primary/40 hover:text-secondary hover:bg-surface transition"
                                        title="Override plan">
                                    <span class="material-symbols-outlined text-[18px]">workspace_premium</span>
                                </button>

                                <!-- Adjust Credits -->
                                <button @click="openCreditsModal('{{ $user->id }}', '{{ $user->name }}', {{ $used }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-primary/40 hover:text-primary hover:bg-surface transition"
                                        title="Adjust credits">
                                    <span class="material-symbols-outlined text-[18px]">token</span>
                                </button>

                                <!-- Suspend / Activate -->
                                <button @click="openSuspendModal('{{ $user->id }}', '{{ $user->name }}', {{ $user->is_suspended ? 'true' : 'false' }})"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-surface transition {{ $user->is_suspended ? 'text-secondary' : 'text-orange-400' }}"
                                        title="{{ $user->is_suspended ? 'Activate' : 'Suspend' }}">
                                    <span class="material-symbols-outlined text-[18px]">{{ $user->is_suspended ? 'lock_open' : 'block' }}</span>
                                </button>

                                <!-- Delete -->
                                <button @click="openDeleteModal('{{ $user->id }}', '{{ $user->name }}')"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg text-red-400 hover:bg-red-50 transition"
                                        title="Delete permanently">
                                    <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-16 text-center text-sm font-label text-primary/40">
                            No users found matching your filters.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="p-6 border-t border-primary/5 flex items-center justify-between text-sm font-label">
            <div class="text-primary/60">
                Showing <span class="font-bold text-primary">{{ $users->firstItem() ?? 0 }}</span>–<span class="font-bold text-primary">{{ $users->lastItem() ?? 0 }}</span>
                of <span class="font-bold text-primary">{{ number_format($users->total()) }}</span> users
            </div>
            <div>{{ $users->links() }}</div>
        </div>
    </div>

    <!-- ── Override Plan Modal ── -->
    <div x-show="planModal.open" x-cloak
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
        <div @click.outside="planModal.open = false"
             class="bg-white rounded-2xl p-8 w-full max-w-sm shadow-xl">
            <h3 class="text-lg font-headline font-bold text-primary mb-2">Override Plan</h3>
            <p class="text-sm font-label text-primary/60 mb-6">
                Change plan for <span class="font-bold text-primary" x-text="planModal.name"></span>
            </p>
            <form :action="`/admin/users/${planModal.userId}/plan`" method="POST">
                @csrf @method('PATCH')
                <select name="plan" x-model="planModal.currentRole"
                        class="w-full border border-primary/20 rounded-xl px-4 py-3 text-sm font-label text-primary mb-6 focus:outline-none focus:border-primary/40">
                    <option value="basic">Free (Basic)</option>
                    <option value="premium">Premium</option>
                </select>
                <div class="flex space-x-3">
                    <button type="button" @click="planModal.open = false"
                            class="flex-1 py-3 rounded-xl border border-primary/20 text-sm font-label text-primary hover:bg-surface transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 rounded-xl bg-primary text-white text-sm font-label hover:bg-primary/90 transition">
                        Update Plan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Adjust Credits Modal ── -->
    <div x-show="creditsModal.open" x-cloak
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
        <div @click.outside="creditsModal.open = false"
             class="bg-white rounded-2xl p-8 w-full max-w-sm shadow-xl">
            <h3 class="text-lg font-headline font-bold text-primary mb-2">Adjust AI Credits Used</h3>
            <p class="text-sm font-label text-primary/60 mb-6">
                Set <span class="font-bold text-primary" x-text="creditsModal.name"></span>'s consumed credit count.
            </p>
            <form :action="`/admin/users/${creditsModal.userId}/credits`" method="POST">
                @csrf @method('PATCH')
                <input type="number" name="ai_quota_used" :value="creditsModal.used" min="0"
                       class="w-full border border-primary/20 rounded-xl px-4 py-3 text-sm font-label text-primary mb-6 focus:outline-none focus:border-primary/40">
                <div class="flex space-x-3">
                    <button type="button" @click="creditsModal.open = false"
                            class="flex-1 py-3 rounded-xl border border-primary/20 text-sm font-label text-primary hover:bg-surface transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 rounded-xl bg-primary text-white text-sm font-label hover:bg-primary/90 transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Suspend / Activate Modal ── -->
    <div x-show="suspendModal.open" x-cloak
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
        <div @click.outside="suspendModal.open = false"
             class="bg-white rounded-2xl p-8 w-full max-w-sm shadow-xl">
            <h3 class="text-lg font-headline font-bold text-primary mb-2"
                x-text="suspendModal.isSuspended ? 'Activate Account' : 'Suspend Account'"></h3>
            <p class="text-sm font-label text-primary/60 mb-6">
                <span x-text="suspendModal.isSuspended
                    ? `Activate ${suspendModal.name}? They will be able to log in again.`
                    : `Suspend ${suspendModal.name}? They will be logged out immediately.`">
                </span>
            </p>
            <form :action="`/admin/users/${suspendModal.userId}/suspend`" method="POST">
                @csrf @method('PATCH')
                <div class="flex space-x-3">
                    <button type="button" @click="suspendModal.open = false"
                            class="flex-1 py-3 rounded-xl border border-primary/20 text-sm font-label text-primary hover:bg-surface transition">
                        Cancel
                    </button>
                    <button type="submit"
                            :class="suspendModal.isSuspended
                                ? 'bg-secondary text-white'
                                : 'bg-orange-500 text-white'"
                            class="flex-1 py-3 rounded-xl text-sm font-label hover:opacity-90 transition">
                        <span x-text="suspendModal.isSuspended ? 'Activate' : 'Suspend'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Delete Modal ── -->
    <div x-show="deleteModal.open" x-cloak
         class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4">
        <div @click.outside="deleteModal.open = false"
             class="bg-white rounded-2xl p-8 w-full max-w-sm shadow-xl">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-4">
                <span class="material-symbols-outlined text-red-500">delete_forever</span>
            </div>
            <h3 class="text-lg font-headline font-bold text-primary mb-2">Delete Permanently</h3>
            <p class="text-sm font-label text-primary/60 mb-6">
                This will permanently delete <span class="font-bold text-primary" x-text="deleteModal.name"></span>
                and all their data. This action cannot be undone.
            </p>
            <form :action="`/admin/users/${deleteModal.userId}`" method="POST">
                @csrf @method('DELETE')
                <div class="flex space-x-3">
                    <button type="button" @click="deleteModal.open = false"
                            class="flex-1 py-3 rounded-xl border border-primary/20 text-sm font-label text-primary hover:bg-surface transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="flex-1 py-3 rounded-xl bg-red-500 text-white text-sm font-label hover:bg-red-600 transition">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

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
        },
        openCreditsModal(id, name, used) {
            this.creditsModal = { open: true, userId: id, name, used };
        },
        openSuspendModal(id, name, isSuspended) {
            this.suspendModal = { open: true, userId: id, name, isSuspended };
        },
        openDeleteModal(id, name) {
            this.deleteModal = { open: true, userId: id, name };
        },
    };
}
</script>
@endpush
