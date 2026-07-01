@props([
    'title' => 'Premium feature',
    'description' => 'Upgrade to Premium to unlock this feature.',
    'href' => route('user.upgrade-quota'),
    'tooltipId' => null,
    'align' => 'center',
])

@php
    $tooltipId = $tooltipId ?: 'premium-lock-' . uniqid();
    $panelAlign = $align === 'left' ? 'left-0' : ($align === 'right' ? 'right-0' : 'left-1/2 -translate-x-1/2');
@endphp

<div {{ $attributes->merge(['class' => 'group/premium-lock relative inline-flex']) }}>
    <div class="inline-flex min-h-11 items-center justify-center"
         tabindex="0"
         role="button"
         aria-disabled="true"
         aria-describedby="{{ $tooltipId }}">
        {{ $slot }}
    </div>

    <div id="{{ $tooltipId }}"
         role="tooltip"
         class="pointer-events-none absolute bottom-full {{ $panelAlign }} z-50 mb-3 w-72 rounded-lg border border-[#A16207]/20 bg-tertiary/95 p-4 text-left opacity-0 shadow-xl backdrop-blur transition duration-200 ease-out group-hover/premium-lock:pointer-events-auto group-hover/premium-lock:opacity-100 group-focus-within/premium-lock:pointer-events-auto group-focus-within/premium-lock:opacity-100">
        <div class="mb-2 flex items-center gap-2">
            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#A16207]/10 text-[#7C4A03]">
                <span class="material-symbols-outlined text-[17px] icon-filled" aria-hidden="true">lock</span>
            </span>
            <div>
                <p class="text-xs font-label font-bold uppercase tracking-widest text-[#7C4A03]">Locked Premium</p>
                <p class="text-sm font-bold text-primary">{{ $title }}</p>
            </div>
        </div>
        <p class="mb-3 text-sm leading-relaxed text-primary/70">{{ $description }}</p>
        <a href="{{ $href }}"
           class="pointer-events-auto inline-flex min-h-11 items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-bold text-tertiary transition hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-[#A16207]/40">
            <span class="material-symbols-outlined text-[16px] icon-filled" aria-hidden="true">workspace_premium</span>
            Upgrade to Unlock
        </a>
    </div>
</div>
