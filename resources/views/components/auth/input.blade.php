@props(['name', 'label' => '', 'type' => 'text', 'placeholder' => '', 'required' => false, 'value' => null, 'id' => null, 'autocomplete' => null, 'hint' => null])

@php
    $inputId = $id ?? $name;
    $errorId = $inputId . '-error';
    $hintId = $hint ? $inputId . '-hint' : null;
    $invalid = $errors->has($name);
@endphp

<div class="space-y-1.5">
    <div class="flex justify-between items-center">
        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant" for="{{ $inputId }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500" aria-hidden="true">*</span>
            @endif
        </label>
        {{ $extraLabel ?? '' }}
    </div>
    <input
        {{ $attributes->merge([
            'class' => 'w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all placeholder:text-outline/50' . ($errors->has($name) ? ' border-red-500 ring-1 ring-red-500' : ''),
            'id' => $inputId,
            'name' => $name,
            'type' => $type,
            'placeholder' => $placeholder,
            'value' => old($name, $value),
            'required' => $required,
            'autocomplete' => $autocomplete,
            'aria-invalid' => $invalid ? 'true' : 'false',
            'aria-describedby' => trim(($invalid ? $errorId : '') . ' ' . ($hintId ?? '')) ?: null,
        ]) }} />
    @if($hint)
        <p id="{{ $hintId }}" class="text-xs text-on-surface-variant/70">{{ $hint }}</p>
    @endif
    @error($name)
        <p id="{{ $errorId }}" class="text-xs text-red-600" role="alert">{{ $message }}</p>
    @enderror
</div>
