@props(['label', 'name' => '', 'value' => ''])

<div class="relative">
    <label class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 block">{{ $label }}</label>
    <input name="{{ $name }}" class="w-full border-b border-primary/20 focus:border-primary bg-transparent py-2 px-0 outline-none transition-all focus:ring-0 text-primary" type="text" value="{{ $value }}" />
</div>