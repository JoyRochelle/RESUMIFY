@extends('layouts.user.app')

@section('title', __('messages.tickets.list.heading') . ' - Resumify')

@section('content')
    <main class="flex-1 overflow-y-auto custom-scrollbar bg-primary/5 pb-20 md:pb-0">

        <div class="max-w-3xl mx-auto px-4 sm:px-6 md:px-12 py-8 md:py-16">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-8">
                <div>
                    <div class="flex items-center space-x-2 text-sm font-label text-primary/50 mb-2">
                        <a href="{{ route('user.help') }}" class="hover:text-primary transition-colors">{{ __('messages.tickets.list.breadcrumb_help') }}</a>
                        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                        <span class="text-primary">{{ __('messages.tickets.list.breadcrumb_current') }}</span>
                    </div>
                    <h1 class="text-2xl font-headline font-bold text-primary">{{ __('messages.tickets.list.heading') }}</h1>
                </div>
                <a href="{{ route('user.help') }}#contact"
                   class="flex items-center space-x-2 text-sm font-label text-white bg-primary px-4 py-2 rounded-xl shadow-sm hover:bg-primary/90 transition">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span>{{ __('messages.tickets.list.new_ticket') }}</span>
                </a>
            </div>

            @if(session('success'))
                <div class="bg-secondary/10 border border-secondary/20 text-secondary text-sm font-label px-5 py-3 rounded-xl flex items-center space-x-2 mb-6">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            {{-- Ticket List --}}
            <div class="bg-white rounded-3xl border border-primary/5 shadow-sm overflow-hidden">
                @forelse($tickets as $ticket)
                @php
                    $badgeMap = [
                        'open'    => 'bg-red-100 text-red-600',
                        'pending' => 'bg-amber-100 text-amber-600',
                        'awaiting_closure' => 'bg-blue-100 text-blue-600',
                        'closed'  => 'bg-primary/10 text-primary/50',
                    ];
                @endphp
                <a href="{{ route('help.tickets.show', $ticket) }}"
                   class="flex items-center justify-between px-6 py-5 border-b border-primary/5 hover:bg-surface/40 transition-colors last:border-b-0">
                    <div class="flex-1 min-w-0 mr-4">
                        <p class="text-sm font-label font-semibold text-primary truncate">{{ $ticket->subject }}</p>
                        <p class="text-[11px] font-label text-primary/40 mt-0.5">{{ $ticket->created_at->diffForHumans() }} &middot; {{ trans_choice('messages.tickets.list.replies_count', $ticket->replies_count, ['count' => $ticket->replies_count]) }}</p>
                    </div>
                    <div class="flex items-center space-x-3 flex-shrink-0">
                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeMap[$ticket->status] ?? '' }}">
                            {{ __('messages.tickets.status.' . $ticket->status) }}
                        </span>
                        <span class="material-symbols-outlined text-primary/30 text-[20px]">chevron_right</span>
                    </div>
                </a>
                @empty
                <div class="py-16 text-center">
                    <span class="material-symbols-outlined text-primary/20 text-[48px] block mb-2">confirmation_number</span>
                    <p class="text-sm font-label text-primary/40 mb-4">{{ __('messages.tickets.list.empty_title') }}</p>
                    <a href="{{ route('user.help') }}#contact"
                       class="text-sm font-label text-primary/60 hover:text-primary underline underline-offset-2">
                        {{ __('messages.tickets.list.empty_cta') }}
                    </a>
                </div>
                @endforelse
            </div>

            @if($tickets->hasPages())
                <div class="mt-6">{{ $tickets->links() }}</div>
            @endif

        </div>

        <footer class="pb-12 text-center text-primary/40 text-sm">
            <p>{{ __('messages.common.footer_copyright') }}</p>
        </footer>
    </main>
@endsection
