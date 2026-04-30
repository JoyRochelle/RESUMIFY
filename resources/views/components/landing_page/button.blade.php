@props(['variant' => 'primary', 'icon' => null])

@php
    $classes = [
        /* Primary: Solid Deep Brown dengan rounded-sm (4px) sesuai DESIGN.md */
        'primary' => 'group flex items-center justify-center gap-3 bg-primary text-tertiary px-8 py-3 rounded-sm text-lg font-bold transition-all hover:opacity-90 active:scale-95 shadow-sm',
        
        /* Secondary: Emerald Green khusus untuk aksi AI (Generate/Refine) */
        'secondary' => 'group flex items-center justify-center gap-3 bg-secondary text-white px-8 py-3 rounded-sm text-lg font-bold transition-all hover:opacity-90 active:scale-95 shadow-md',
        
        /* Outline: Menggunakan border tipis 'Ghost Border' (outline-variant) */
        'outline' => 'group flex items-center justify-center gap-3 border border-outline/20 text-primary px-8 py-3 rounded-sm text-lg font-bold transition-all hover:bg-surface-container-low active:scale-95',
        
        /* Nav: Versi ringkas untuk Navbar */
        'nav' => 'bg-primary text-tertiary px-6 py-2.5 rounded-sm font-bold shadow-sm active:opacity-80 active:scale-95 transition-all text-sm'
    ];
@endphp

<button {{ $attributes->merge(['class' => $classes[$variant] ?? $classes['primary']]) }}>
    <span class="font-body">{{ $slot }}</span>
    @if($icon)
        <span class="material-symbols-outlined transition-transform group-hover:translate-x-1 !text-[20px]">
            {{ $icon }}
        </span>
    @endif
</button>