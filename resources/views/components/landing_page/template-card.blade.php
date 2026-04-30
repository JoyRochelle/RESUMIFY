@props(['title', 'category', 'badge' => null, 'badgeColor' => 'secondary'])

{{-- Template Card: Menggunakan rounded-sm (4px) sesuai pedoman kelima DESIGN.md --}}
<div class="group bg-surface-container-lowest rounded-sm border border-primary/5 p-2 transition-all duration-500 hover:shadow-xl">
    
    {{-- Preview Area: Background shifting ke surface-container-low sesuai pedoman kedua DESIGN.md --}}
    <div class="aspect-[1/1.41] bg-surface-container-low rounded-sm overflow-hidden relative flex flex-col p-6 border border-primary/5">
        
        <div class="opacity-60 group-hover:opacity-100 transition-opacity duration-500">
            {{ $slot }}
        </div>

        {{-- Overlay menggunakan backdrop-blur 12px sesuai aturan Glassmorphism --}}
        <div class="absolute inset-0 bg-primary/60 backdrop-blur-[12px] opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center justify-center text-white">
            <x-landing_page.button variant="secondary">
                Use Template
            </x-landing_page.button>
        </div>
    </div>

    <div class="px-2 py-3">
        {{-- Title and Badge Row --}}
        <div class="flex items-center gap-2 mb-1">
            {{-- Menambahkan tracking-tighter pada font-headline (Newsreader) --}}
            <h3 class="text-lg font-headline font-bold text-primary tracking-tighter">{{ $title }}</h3>
            
            @if($badge)
                @php
                    $badgeClasses = match($badgeColor) {
                        'blue' => 'bg-blue-500 text-white',
                        'purple' => 'bg-purple-500 text-white',
                        default => 'bg-secondary text-white',
                    };
                @endphp
                <span class="inline-flex items-center gap-1 {{ $badgeClasses }} text-[9px] font-bold px-2 py-0.5 rounded-sm uppercase tracking-wider font-body whitespace-nowrap">
                    @if($badgeColor === 'blue')
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-white"></span>
                    @endif
                    @if($badgeColor === 'purple')
                        <span class="text-[10px]">✦</span>
                    @endif
                    {{ $badge }}
                </span>
            @endif
        </div>
        
        {{-- Category Label: Tracking wider 0.2em untuk kategori --}}
        <p class="text-[10px] font-bold text-primary/50 uppercase tracking-[0.2em] font-body">{{ $category }}</p>
    </div>
</div>