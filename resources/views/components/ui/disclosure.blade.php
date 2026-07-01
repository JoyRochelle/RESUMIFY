@props([
    'title',
    'icon' => null,
    'open' => false,
    'id' => null,
])

@php
    $panelId = $id ?? 'disclosure-' . md5($title . spl_object_id($slot));
@endphp

<section x-data="{ open: {{ $open ? 'true' : 'false' }} }" {{ $attributes->merge(['class' => 'rounded-lg border border-primary/10 bg-tertiary shadow-sm']) }}>
    <h3>
        <button type="button"
                class="flex w-full items-center justify-between gap-4 rounded-lg p-5 text-left transition hover:bg-primary/5 focus:outline-none focus:ring-2 focus:ring-secondary/40"
                :class="open ? 'bg-surface-container-low rounded-b-none border-b border-primary/10' : ''"
                aria-controls="{{ $panelId }}"
                x-bind:aria-expanded="open.toString()"
                x-on:click="open = !open">
            <span class="flex items-center gap-3 font-bold text-primary">
                @if($icon)
                    <span class="material-symbols-outlined text-primary/70" aria-hidden="true">{{ $icon }}</span>
                @endif
                {{ $title }}
            </span>
            <span class="material-symbols-outlined text-primary/60 transition-transform" :class="open ? 'rotate-180' : ''" aria-hidden="true">expand_more</span>
        </button>
    </h3>

    <div id="{{ $panelId }}" x-show="open" x-collapse x-cloak>
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</section>
