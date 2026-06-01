@extends('layouts.user.app')

@section('title', 'Ticket #' . substr($ticket->id, -8) . ' - Resumify')

@section('content')
    <main class="flex-1 overflow-y-auto custom-scrollbar bg-primary/5 pb-20 md:pb-0">

        <div class="max-w-3xl mx-auto px-4 sm:px-6 md:px-12 py-8 md:py-16">

            {{-- Breadcrumb --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <div class="flex items-center space-x-2 text-sm font-label text-primary/50 mb-2">
                        <a href="{{ route('user.help') }}" class="hover:text-primary transition-colors">Help Center</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <a href="{{ route('help.tickets') }}" class="hover:text-primary transition-colors">My Tickets</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="text-primary">Ticket #{{ substr($ticket->id, -8) }}</span>
                    </div>
                    <h1 class="text-xl font-headline font-bold text-primary">{{ $ticket->subject }}</h1>
                </div>
                @php $badgeMap = ['open' => 'bg-red-100 text-red-600', 'pending' => 'bg-amber-100 text-amber-600', 'closed' => 'bg-primary/10 text-primary/50']; @endphp
                <span class="inline-block px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeMap[$ticket->status] ?? '' }} flex-shrink-0">
                    {{ $ticket->status }}
                </span>
            </div>

            {{-- Conversation Thread --}}
            <div class="bg-white rounded-3xl border border-primary/5 shadow-sm overflow-hidden mb-6">
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
                        <p class="text-sm font-label text-primary/40">No replies yet. We'll get back to you soon.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Ticket Info --}}
            <div class="bg-white rounded-3xl border border-primary/5 shadow-sm p-6 text-sm font-label text-primary/60 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                    <span>Opened {{ $ticket->created_at->format('d M Y') }}</span>
                </div>
                @if($ticket->status === 'closed')
                    <span class="flex items-center space-x-1 text-primary/40">
                        <span class="material-symbols-outlined text-[16px]">lock</span>
                        <span>This ticket is closed</span>
                    </span>
                @else
                    <span class="text-primary/40 text-xs">Awaiting support response</span>
                @endif
            </div>

        </div>

        <footer class="pb-12 text-center text-primary/40 text-sm">
            <p>© 2026 Resumify - Curated with Integrity</p>
        </footer>
    </main>
@endsection
