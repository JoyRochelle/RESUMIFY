@props(['name', 'label' => '', 'type' => 'text', 'placeholder' => '', 'required' => false, 'value' => null])

<div class="space-y-1.5">
    <div class="flex justify-between items-center">
        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant" for="{{ $name }}">
            {{ $label }}
        </label>
        {{ $extraLabel ?? '' }}
    </div>
    <input
        {{ $attributes->merge([
            'class' => 'w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all placeholder:text-outline/50' . ($errors->has($name) ? ' border-red-500 ring-1 ring-red-500' : ''),
            'id' => $name,
            'name' => $name,
            'type' => $type,
            'placeholder' => $placeholder,
            'value' => old($name, $value),
            'required' => $required,
        ]) }} />
</div>
