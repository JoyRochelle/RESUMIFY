@props([
    'score' => 0,
    'size' => 'lg',
    'showPercent' => true
])

@php
    $isLg = $size === 'lg';
    $r = $isLg ? 56 : 28;
    $center = $isLg ? 64 : 32;
    $strokeWidth = $isLg ? 8 : 4;
    $circumference = round(2 * pi() * $r, 1);
    $dashoffset = round($circumference * (1 - $score / 100), 1);
    $containerClass = $isLg ? 'w-32 h-32' : 'w-16 h-16';
    $textClass = $isLg
        ? 'font-headline text-3xl font-bold text-primary italic'
        : 'font-headline font-bold text-primary';
@endphp

<div class="relative {{ $containerClass }} flex items-center justify-center">
    <svg class="w-full h-full transform -rotate-90">
        <circle class="text-primary/10" cx="{{ $center }}" cy="{{ $center }}" fill="transparent" r="{{ $r }}" stroke="currentColor" stroke-width="{{ $strokeWidth }}"></circle>
        <circle class="text-secondary" cx="{{ $center }}" cy="{{ $center }}" fill="transparent" r="{{ $r }}" stroke="currentColor" stroke-dasharray="{{ $circumference }}" stroke-dashoffset="{{ $dashoffset }}" stroke-linecap="round" stroke-width="{{ $strokeWidth }}"></circle>
    </svg>
    <span class="absolute {{ $textClass }}">{{ $score }}{{ $showPercent ? '%' : '' }}</span>
</div>
