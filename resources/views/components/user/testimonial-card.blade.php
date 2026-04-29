@props(['quote', 'name', 'role', 'avatar'])

<div class="lg:col-span-2 bg-primary p-12 rounded-2xl flex flex-col justify-center relative overflow-hidden">
    <div class="relative z-10">
        <p class="font-headline italic text-2xl text-tertiary mb-6 leading-relaxed">
            "{{ $quote }}"
        </p>
        <div class="flex items-center gap-4">
            <img alt="{{ $name }}" class="w-12 h-12 rounded-full border-2 border-tertiary/30 object-cover" src="{{ $avatar }}"/>
            <div>
                <p class="text-tertiary font-bold">{{ $name }}</p>
                <p class="text-tertiary/80 text-xs">{{ $role }}</p>
            </div>
        </div>
    </div>
    <div class="absolute right-[-20px] bottom-[-20px] opacity-10">
        <span class="material-symbols-outlined text-[160px] text-tertiary">format_quote</span>
    </div>
</div>
