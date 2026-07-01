@props([
    'user' => auth()->user(),
    'size' => 'sm',
    'surface' => 'light',
])

@php
    $isPremium = $user?->isPremium();
    $label = $isPremium ? 'Premium Member' : 'Basic Member';
    $icon = $isPremium ? 'workspace_premium' : 'person';
    $sizeClasses = $size === 'xs'
        ? 'px-2 py-0.5 text-[9px] gap-1'
        : 'px-3 py-1 text-[10px] gap-1.5';
    $iconSize = $size === 'xs' ? 'text-[12px]' : 'text-[14px]';
    $toneClasses = match($surface) {
        'dark' => $isPremium
            ? 'border-[#FACC15]/35 bg-[#FACC15]/15 text-[#FEF3C7] shadow-[0_1px_0_rgba(255,255,255,0.12)_inset]'
            : 'border-white/20 bg-white/10 text-white shadow-[0_1px_0_rgba(255,255,255,0.1)_inset]',
        default => $isPremium
            ? 'border-[#A16207]/30 bg-[#A16207]/10 text-[#7C4A03] shadow-[0_1px_0_rgba(255,255,255,0.7)_inset]'
            : 'border-primary/15 bg-primary/5 text-primary/70',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full border font-label font-bold uppercase tracking-widest {$sizeClasses} {$toneClasses}"]) }}>
    <span class="material-symbols-outlined {{ $iconSize }} {{ $isPremium ? 'icon-filled' : '' }}" aria-hidden="true">{{ $icon }}</span>
    <span>{{ $label }}</span>
</span>
