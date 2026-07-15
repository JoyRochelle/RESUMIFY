@props(['title', 'category', 'badge' => null, 'badgeColor' => 'secondary'])

<div class="group bg-surface-container-lowest rounded-lg border border-primary/5 p-2 transition-all duration-500 hover:shadow-xl">

    {{-- Preview Area: identik dengan dashboard resume-card --}}
    <div class="aspect-[210/297] bg-surface-container-low rounded-md overflow-hidden relative border border-primary/5 cursor-pointer">

        {{-- Slot: iframe langsung di sini, sama seperti dashboard --}}
        {{ $slot }}

        {{-- Overlay hover --}}
        <div class="absolute inset-0 bg-primary/60 backdrop-blur-[12px] opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center text-white z-20">
            <span class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm text-white px-6 py-2.5 rounded-full text-sm font-bold border border-white/30 hover:bg-white/30 transition-all">
                <span class="material-symbols-outlined text-[18px]">visibility</span>
                Preview Template
            </span>
        </div>
    </div>

    <div class="px-2 py-3">
        {{-- Title and Badge Row --}}
        <div class="flex items-center gap-2 mb-1">
            <h3 class="text-lg font-headline font-bold text-primary tracking-tighter">{{ $title }}</h3>

            @if($badge)
                @php
                    // ATS compatibility signal: OK -> success (emerald), MED -> warning (amber), LOW -> danger (red).
                    // Matches the semantic vocabulary already used by x-ui.alert's success/warning/error variants.
                    $badgeClasses = match($badgeColor) {
                        'blue' => 'bg-secondary text-white',
                        'amber' => 'bg-amber-500 text-white',
                        'red' => 'bg-red-500 text-white',
                        'slate' => 'bg-slate-700 text-white',
                        'teal' => 'bg-teal-600 text-white',
                        'gray' => 'bg-gray-700 text-white',
                        default => 'bg-secondary text-white',
                    };
                @endphp
                <span class="inline-flex items-center gap-1 {{ $badgeClasses }} text-[9px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider font-body whitespace-nowrap">
                    {{ $badge }}
                </span>
            @endif
        </div>

        {{-- Category Label: Tracking wider 0.2em untuk kategori --}}
        <p class="text-[10px] font-bold text-primary/50 uppercase tracking-[0.2em] font-body">{{ $category }}</p>
    </div>
</div>