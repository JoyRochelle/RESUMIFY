@props(['icon', 'label'])

<div class="flex items-center gap-2">
    <span class="material-symbols-outlined text-primary text-3xl">{{ $icon }}</span>
    <span class="font-body font-bold text-primary">{{ $label }}</span>
</div>
