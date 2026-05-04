@props(['variant' => 'primary', 'icon' => null, 'href' => null])

@php
    $classes = [
        'primary' =>
            'group inline-flex items-center justify-center gap-3 bg-primary text-tertiary px-8 py-3 rounded-sm text-lg font-bold transition-all hover:opacity-90 active:scale-98 shadow-sm',
        'secondary' =>
            'group inline-flex items-center justify-center gap-3 bg-secondary text-white px-8 py-3 rounded-sm text-lg font-bold transition-all hover:opacity-90 active:scale-95 shadow-md',
        'outline' =>
            'group inline-flex items-center justify-center gap-3 border border-outline/20 text-primary px-8 py-3 rounded-sm text-lg font-bold transition-all hover:bg-surface-container-low active:scale-95',
        'nav' =>
            'inline-flex items-center justify-center bg-primary text-tertiary px-6 py-2.5 rounded-sm font-bold shadow-sm active:opacity-80 active:scale-95 transition-all text-sm',
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
