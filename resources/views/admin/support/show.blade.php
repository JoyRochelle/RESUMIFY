@extends('layouts.admin.app')

@section('title', 'Ticket #' . substr($ticket->id, -8) . ' - Support')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-10">

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
           class="flex items-center space-x-2 text-sm font-label text-primary/60 hover:text-primary bg-white border border-primary/10 px-4 py-2 rounded-xl shadow-sm transition-colors">
            <span class="material-symbols-outlined text-[18px]">arrow_back</span>
            <span>Back</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-secondary/10 border border-secondary/20 text-secondary text-sm font-label px-5 py-3 rounded-xl flex items-center space-x-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Thread (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Conversation -->
            <div class="bg-white rounded-3xl shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 overflow-hidden">
                <div class="p-6 border-b border-primary/5">
                    <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest">Conversation</h3>
                </div>

                <div class="divide-y divide-primary/5">
                    @forelse($ticket->replies as $reply)
                    <div class="p-6 {{ $reply->sender?->isAdmin() ? 'bg-primary/[0.02]' : '' }}">
                        <div class="flex items-start space-x-4">
                            <div class="w-9 h-9 rounded-full overflow-hidden flex-shrink-0 bg-primary/10">
                                <img src="{{ $reply->sender?->avatar_url ?? 'https://ui-avatars.com/api/?name=?&background=fcdccb&color=4f3b2f' }}"
                                     alt="{{ $reply->sender?->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="text-sm font-label font-bold text-primary">{{ $reply->sender?->name ?? 'Unknown' }}</span>
                                    @if($reply->sender?->isAdmin())
                                        <span class="text-[9px] bg-primary/10 text-primary/60 px-2 py-0.5 rounded-full uppercase tracking-wider font-bold">Support</span>
                                    @endif
                                    <span class="text-[11px] font-label text-primary/40">{{ $reply->created_at->format('d M Y, H:i') }}</span>
                                </div>
                                <p class="text-sm font-label text-primary/80 whitespace-pre-wrap">{{ $reply->body }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-10 text-center">
                        <span class="material-symbols-outlined text-primary/20 text-[40px] block mb-2">forum</span>
                        <p class="text-sm font-label text-primary/40">No replies yet</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Reply Form -->
            @if($ticket->status !== 'closed')
            <div class="bg-white rounded-3xl shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5 p-6">
                <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">Send Reply</h3>
                <form action="{{ route('admin.support.reply', $ticket) }}" method="POST">
                    @csrf
                    <textarea name="body" rows="5" required
                              placeholder="Type your reply..."
                              class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-3 text-sm font-label text-primary placeholder:text-primary/40 focus:outline-none focus:border-primary/30 resize-none">{{ old('body') }}</textarea>
                    @error('body')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                    <div class="flex justify-end mt-4">
                        <button type="submit"
                                class="bg-primary text-white px-6 py-2.5 rounded-xl text-sm font-label hover:bg-primary/90 transition">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>
            @else
            <div class="bg-primary/5 rounded-3xl p-6 text-center">
                <p class="text-sm font-label text-primary/60">This ticket is closed. Reopen it to reply.</p>
            </div>
            @endif

        </div>

        <!-- Sidebar (1/3 width) -->
        <div class="space-y-6">

            <!-- Ticket Info -->
            <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
                <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">Ticket Info</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-[10px] font-label text-primary/40 uppercase tracking-widest mb-0.5">Status</p>
                        @php $badgeMap = ['open' => 'bg-red-100 text-red-600', 'pending' => 'bg-amber-100 text-amber-600', 'closed' => 'bg-primary/10 text-primary/50']; @endphp
                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeMap[$ticket->status] ?? '' }}">
                            {{ $ticket->status }}
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
            <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
                <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">User</h3>
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
            <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
                <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">Assign To</h3>
                <form action="{{ route('admin.support.assign', $ticket) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="assigned_to"
                            class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-2.5 text-sm font-label text-primary focus:outline-none focus:border-primary/30 mb-3">
                        <option value="">Unassigned</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ $ticket->assigned_to === $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="w-full bg-primary/10 text-primary py-2.5 rounded-xl text-sm font-label hover:bg-primary/20 transition">
                        Update Assignment
                    </button>
                </form>
            </div>

            <!-- Status Toggle -->
            <div class="bg-white rounded-3xl p-6 shadow-[0_2px_10px_rgba(79,59,47,0.03)] border border-primary/5">
                <h3 class="text-[10px] font-label text-primary/60 uppercase tracking-widest mb-4">Update Status</h3>
                <form action="{{ route('admin.support.status', $ticket) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="status"
                            class="w-full bg-surface border border-primary/10 rounded-xl px-4 py-2.5 text-sm font-label text-primary focus:outline-none focus:border-primary/30 mb-3">
                        <option value="open"    {{ $ticket->status === 'open'    ? 'selected' : '' }}>Open</option>
                        <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="closed"  {{ $ticket->status === 'closed'  ? 'selected' : '' }}>Closed</option>
                    </select>
                    <button type="submit"
                            class="w-full bg-primary text-white py-2.5 rounded-xl text-sm font-label hover:bg-primary/90 transition">
                        Update Status
                    </button>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
