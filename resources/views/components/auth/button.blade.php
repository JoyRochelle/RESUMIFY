@props(['loadingText' => 'Processing...'])

<button
    {{ $attributes->merge([
        'type' => 'submit',
        'class' =>
            'w-full bg-primary text-white py-3.5 rounded-lg font-bold hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2',
    ]) }}
    :disabled="loading" :class="loading ? 'opacity-70 cursor-not-allowed' : ''">
    <span x-show="loading" class="animate-spin h-4 w-4 border-2 border-white/30 border-t-white rounded-full"></span>
    <span x-text="loading ? '{{ $loadingText ?? 'Processing...' }}' : '{{ $slot }}'"></span>
</button>
