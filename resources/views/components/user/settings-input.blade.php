@props([
    'label',
    'name' => null,
    'id' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'showToggle' => false,
    'hint' => null,
    'required' => false,
    'autocomplete' => null,
])

@php
    $fieldName = $name ?? $attributes->get('name');
    $inputId = $id ?? ($attributes->get('id') ?: ($fieldName ? 'settings-' . \Illuminate\Support\Str::slug($fieldName) : 'settings-' . md5($label)));
    $errorId = $fieldName ? $inputId . '-error' : null;
    $hintId = $hint ? $inputId . '-hint' : null;
    $invalid = $fieldName ? $errors->has($fieldName) : false;
@endphp

<div class="space-y-1" x-data="{ visible: false }">
    <label for="{{ $inputId }}" class="text-xs font-bold text-primary/60 uppercase tracking-widest">
        {{ $label }}
        @if($required)
            <span class="text-red-500" aria-hidden="true">*</span>
        @endif
    </label>
    <div class="relative">
        <input
            {{ $attributes->merge(['class' => 'w-full bg-transparent border-b border-primary/20 focus:border-primary transition-colors py-2 outline-none font-body text-primary' . ($type !== 'password' ? ' text-lg' : '')]) }}
            id="{{ $inputId }}"
            @if($fieldName) name="{{ $fieldName }}" @endif
            @if($showToggle && $type === 'password')
                x-bind:type="visible ? 'text' : 'password'"
            @else
                type="{{ $type }}"
            @endif
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            aria-invalid="{{ $invalid ? 'true' : 'false' }}"
            @if($errorId || $hintId) aria-describedby="{{ trim(($errorId && $invalid ? $errorId : '') . ' ' . ($hintId ?? '')) }}" @endif
        />
        @if($showToggle)
        <button type="button"
                class="absolute right-0 top-0 inline-flex min-h-11 min-w-11 items-center justify-center rounded-full text-primary/60 hover:text-primary focus:outline-none focus:ring-2 focus:ring-secondary/40"
                x-on:click="visible = !visible"
                x-bind:aria-label="visible ? 'Hide {{ $label }}' : 'Show {{ $label }}'">
            <span class="material-symbols-outlined" aria-hidden="true" x-text="visible ? 'visibility_off' : 'visibility'"></span>
        </button>
        @endif
    </div>
    @if($hint)
        <p id="{{ $hintId }}" class="text-xs text-primary/50">{{ $hint }}</p>
    @endif
    @if($fieldName)
        @error($fieldName)
            <p id="{{ $errorId }}" class="text-xs text-red-600" role="alert">{{ $message }}</p>
        @enderror
    @endif
</div>
