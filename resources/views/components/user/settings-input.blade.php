@props(['label', 'type' => 'text', 'value' => '', 'placeholder' => '', 'showToggle' => false])

<div class="space-y-1">
    <label class="text-xs font-bold text-primary/60 uppercase tracking-widest">{{ $label }}</label>
    <div class="relative">
        <input
            {{ $attributes->merge(['class' => 'w-full bg-transparent border-b border-primary/20 focus:border-primary transition-colors py-2 outline-none font-body text-primary' . ($type !== 'password' ? ' text-lg' : '')]) }}
            type="{{ $type }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
        />
        @if($showToggle)
        <span class="material-symbols-outlined absolute right-0 top-2 text-primary/60 cursor-pointer">visibility</span>
        @endif
    </div>
</div>
