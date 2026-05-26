@props([
    'variant' => 'neutral'
])

@php
    $classes = match($variant) {
        'danger' => 'bg-red-500/10 text-red-600 border-red-500/20',
        'success' => 'bg-secondary/10 text-secondary border-secondary/20',
        default => 'bg-primary/5 text-primary/60 border-primary/10',
    };
@endphp

<span {{ $attributes->merge(['class' => "px-4 py-1.5 rounded-full text-xs font-bold border $classes"]) }}>
    {{ $slot }}
</span>
