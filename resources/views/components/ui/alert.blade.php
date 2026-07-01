@props([
    'variant' => 'info',
    'title' => null,
    'icon' => null,
])

@php
    $styles = [
        'success' => ['class' => 'border-secondary/30 bg-secondary/10 text-secondary', 'icon' => 'check_circle'],
        'error' => ['class' => 'border-red-300 bg-red-50 text-red-700', 'icon' => 'error'],
        'warning' => ['class' => 'border-amber-300 bg-amber-50 text-amber-800', 'icon' => 'warning'],
        'info' => ['class' => 'border-primary/15 bg-tertiary text-primary', 'icon' => 'info'],
    ];
    $style = $styles[$variant] ?? $styles['info'];
    $resolvedIcon = $icon ?? $style['icon'];
@endphp

<div {{ $attributes->merge([
    'class' => 'rounded-lg border px-4 py-3 shadow-sm ' . $style['class'],
    'role' => $variant === 'error' ? 'alert' : 'status',
    'aria-live' => $variant === 'error' ? 'assertive' : 'polite',
]) }}>
    <div class="flex gap-3">
        <span class="material-symbols-outlined icon-filled mt-0.5 text-[20px]" aria-hidden="true">{{ $resolvedIcon }}</span>
        <div class="min-w-0">
            @if($title)
                <p class="font-label text-sm font-bold">{{ $title }}</p>
            @endif
            <div class="text-sm leading-6 {{ $title ? 'mt-1' : '' }}">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
