@props(['variant' => 'primary', 'icon' => null, 'href' => null])

@php
    $classes = [
        'primary' =>
            'group inline-flex items-center justify-center gap-3 bg-primary text-tertiary px-8 py-3 rounded-lg text-lg font-bold transition-all hover:opacity-90 active:scale-95 shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40',
        'secondary' =>
            'group inline-flex items-center justify-center gap-3 bg-secondary text-white px-8 py-3 rounded-lg text-lg font-bold transition-all hover:opacity-90 active:scale-95 shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40',
        'outline' =>
            'group inline-flex items-center justify-center gap-3 border border-primary/20 text-primary px-8 py-3 rounded-lg text-lg font-bold transition-all hover:bg-surface-container-low active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40',
        'nav' =>
            'inline-flex items-center justify-center bg-primary text-tertiary px-6 py-2.5 rounded-lg font-bold shadow-sm active:opacity-80 active:scale-95 transition-all text-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-secondary/40',
        'light' =>
            'inline-flex items-center justify-center gap-3 bg-white text-primary px-6 sm:px-8 py-3 rounded-lg text-sm font-bold transition-all hover:bg-white/90 active:scale-95 shadow-sm focus:outline-none focus-visible:ring-2 focus-visible:ring-white/60',
    ];
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes[$variant] ?? $classes['primary']]) }}>
        <span class="font-body">{{ $slot }}</span>
        @if ($icon)
            <span class="material-symbols-outlined transition-transform group-hover:translate-x-1 !text-[20px]">
                {{ $icon }}
            </span>
        @endif
    </a>
@else
    <button {{ $attributes->merge(['class' => $classes[$variant] ?? $classes['primary']]) }}>
        <span class="font-body">{{ $slot }}</span>
        @if ($icon)
            <span class="material-symbols-outlined transition-transform group-hover:translate-x-1 !text-[20px]">
                {{ $icon }}
            </span>
        @endif
    </button>
@endif
