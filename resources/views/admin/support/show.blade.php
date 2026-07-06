@extends('layouts.admin.app')

@section('title', 'Ticket #' . substr($ticket->id, -8) . ' - Support')

@section('content')
<div class="mx-auto w-full max-w-4xl space-y-8 pb-24 md:pb-12">

    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-sm font-label text-primary/50 mb-2">
                <a href="{{ route('admin.support') }}" class="hover:text-primary transition-colors">Support Tickets</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary">Ticket #{{ substr($ticket->id, -8) }}</span>
            </div>
            <h1 class="text-2xl font-headline font-bold text-primary">{{ $ticket->subject }}</h1>
        </div>
        <a href="{{ route('admin.support') }}"
           class="admin-btn-secondary">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Back</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Thread (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Conversation + Reply (Livewire chat) -->
            <livewire:ticket-chat :ticket="$ticket" />

        </div>

        <!-- Sidebar (1/3 width) -->
        <div class="space-y-6">

            <!-- Ticket Info -->
            <div class="admin-card-pad">
                <h3 class="admin-section-title mb-4">Ticket Info</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-0.5">Status</p>
                        @php $badgeMap = ['open' => 'bg-red-100 text-red-600', 'pending' => 'bg-amber-100 text-amber-600', 'awaiting_closure' => 'bg-blue-100 text-blue-600', 'closed' => 'bg-primary/10 text-primary/50']; @endphp
                        <span class="admin-badge {{ $badgeMap[$ticket->status] ?? '' }}">
                            {{ str_replace('_', ' ', $ticket->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-0.5">Created</p>
                        <p class="text-sm font-label text-primary">{{ $ticket->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-0.5">Replies</p>
                        <p class="text-sm font-label text-primary">{{ $ticket->replies->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- User Info -->
            <div class="admin-card-pad">
                <h3 class="admin-section-title mb-4">User</h3>
                <div class="flex items-center space-x-3 mb-3">
                    <div class="w-10 h-10 rounded-full overflow-hidden bg-primary/10 flex-shrink-0">
                        <img src="{{ $ticket->user?->avatar_url ?? 'https://ui-avatars.com/api/?name=?&background=fcdccb&color=4f3b2f' }}"
                             class="w-full h-full object-cover">
                    </div>
                    <div>
                        <p class="text-sm font-label font-bold text-primary">{{ $ticket->user?->name ?? '—' }}</p>
                        <p class="text-[11px] font-label text-primary/50">{{ $ticket->user?->email ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Assign -->
            <div class="admin-card-pad">
                <h3 class="admin-section-title mb-4">Assign To</h3>
                <form action="{{ route('admin.support.assign', $ticket) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="assigned_to"
                            class="admin-filter-field mb-3 w-full">
                        <option value="">Unassigned</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-ui.loading-button variant="outline" loading-text="Updating..." icon="assignment_ind" class="w-full">Update Assignment</x-ui.loading-button>
                </form>
            </div>

            <!-- Status Toggle -->
            <div class="admin-card-pad">
                <h3 class="admin-section-title mb-4">Update Status</h3>
                <form action="{{ route('admin.support.status', $ticket) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="status"
                            class="admin-filter-field mb-3 w-full">
                        <option value="open"    {{ $ticket->status === 'open'    ? 'selected' : '' }}>Open</option>
                        <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="closed"  {{ $ticket->status === 'closed'  ? 'selected' : '' }}>Closed</option>
                    </select>
                    <x-ui.loading-button loading-text="Updating..." icon="published_with_changes" class="w-full">Update Status</x-ui.loading-button>
                </form>
            </div>

            <!-- Mutual Close Flow -->
            <div class="admin-card-pad">
                <h3 class="admin-section-title mb-4">Close Request</h3>
                @if(in_array($ticket->status, ['open', 'pending']))
                    <form action="{{ route('admin.support.request-close', $ticket) }}" method="POST">
                        @csrf @method('PATCH')
                        <x-ui.loading-button variant="outline" loading-text="Requesting..." icon="task_alt" class="w-full">Request Close</x-ui.loading-button>
                    </form>
                @elseif($ticket->status === 'awaiting_closure')
                    @if($ticket->close_requested_by === auth()->id())
                        <p class="text-sm font-label text-primary/60">Waiting for the user to confirm or reject your close request.</p>
                    @else
                        <div class="flex gap-2">
                            <form action="{{ route('admin.support.reject-close', $ticket) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <x-ui.loading-button variant="outline" loading-text="Rejecting..." icon="close" class="w-full">Reject</x-ui.loading-button>
                            </form>
                            <form action="{{ route('admin.support.confirm-close', $ticket) }}" method="POST" class="flex-1">
                                @csrf @method('PATCH')
                                <x-ui.loading-button loading-text="Confirming..." icon="check" class="w-full">Confirm</x-ui.loading-button>
                            </form>
                        </div>
                    @endif
                @else
                    <p class="text-sm font-label text-primary/60">This ticket is closed.</p>
                @endif
            </div>

        </div>
    </div>

</div>
@endsection
