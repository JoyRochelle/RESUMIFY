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
                @php $badgeMap = ['open' => 'bg-red-100 text-red-600', 'pending' => 'bg-amber-100 text-amber-600', 'awaiting_closure' => 'bg-blue-100 text-blue-600', 'closed' => 'bg-primary/10 text-primary/50']; @endphp
                <span class="inline-block px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeMap[$ticket->status] ?? '' }} flex-shrink-0">
                    {{ str_replace('_', ' ', $ticket->status) }}
                </span>
            </div>

            {{-- Conversation Thread --}}
            <livewire:ticket-chat :ticket="$ticket" />

            {{-- Ticket Info --}}
            <div class="pt-6 mt-6 bg-white rounded-3xl border border-primary/5 shadow-sm p-6 text-sm font-label text-primary/60 flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span class="material-symbols-outlined text-[16px]">schedule</span>
                    <span>Opened {{ $ticket->created_at->format('d M Y') }}</span>
                </div>
            </div>

        </div>

        <footer class="pb-12 text-center text-primary/40 text-sm">
            <p>© 2026 Resumify - Curated with Integrity</p>
        </footer>
    </main>
@endsection
