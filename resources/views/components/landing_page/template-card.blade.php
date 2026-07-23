@props(['title', 'category', 'badge' => null, 'badgeColor' => 'secondary'])

<div class="group bg-surface-container-lowest rounded-lg border border-primary/5 p-2 transition-all duration-200 ease-out hover:shadow-lg hover:-translate-y-1">

    {{-- Preview Area: identik dengan dashboard resume-card --}}
    <div class="aspect-[210/297] bg-surface-container-low rounded-md overflow-hidden relative border border-primary/5 cursor-pointer">

        {{-- Slot: iframe langsung di sini, sama seperti dashboard --}}
        {{ $slot }}

        {{-- Overlay hover: same gradient + emerald pill vocabulary as the dashboard resume cards --}}
        <div class="absolute inset-0 bg-gradient-to-t from-primary/70 via-primary/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-end justify-center pb-6 z-20">
            <span class="inline-flex items-center gap-2 bg-secondary text-white px-5 py-2 rounded-full text-sm font-bold shadow-sm">
                <span class="material-symbols-outlined text-[18px]" aria-hidden="true">visibility</span>
                {{ __('messages.landing.templates.preview_template') }}
            </span>
        </div>
    </div>

    <div class="px-2 py-3">
        {{-- Title and Badge Row --}}
        <div class="flex items-center gap-2 mb-1">
            <h3 class="text-lg font-headline font-bold text-primary tracking-tight">{{ $title }}</h3>

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