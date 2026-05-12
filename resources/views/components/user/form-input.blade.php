@props([
    'label',
    'name'        => '',
    'value'       => '',
    'type'        => 'text',
    'placeholder' => '',
    'required'    => false,
    'hint'        => '',
    'maxlength'   => null,
])

<div class="relative group/input" x-data="{ focused: false, error: '' }">
    <label class="text-[11px] font-bold uppercase tracking-wider transition-colors duration-200 mb-1 flex items-center gap-1"
           :class="error ? 'text-red-500' : (focused ? 'text-secondary' : 'text-primary/60')">
        {{ $label }}
        @if($required)
            <span class="text-red-400 text-[10px]">*</span>
        @endif
    </label>

    <div class="relative">
        <input
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
            {{ $attributes->merge(['class' => 'w-full border-b-2 border-primary/15 focus:border-secondary bg-transparent py-2 px-0 outline-none transition-all duration-200 focus:ring-0 text-primary text-sm placeholder:text-primary/30']) }}
            @focus="focused = true; error = ''"
            @blur="focused = false; validate($event, '{{ $type }}', {{ $required ? 'true' : 'false' }})"
            x-on:input="if(error) validate($event, '{{ $type }}', {{ $required ? 'true' : 'false' }})"
        />
        {{-- Valid checkmark --}}
        <span class="absolute right-0 top-2 text-emerald-500 text-[16px] material-symbols-outlined transition-all duration-200"
              x-show="!error && $el.previousElementSibling.value && !focused"
              style="display:none">
            check_circle
        </span>
        {{-- Error icon --}}
        <span class="absolute right-0 top-2 text-red-400 text-[16px] material-symbols-outlined transition-all duration-200"
              x-show="error"
              style="display:none">
            error
        </span>
    </div>

    {{-- Error message --}}
    <p class="text-[11px] text-red-400 mt-1 leading-tight" x-show="error" x-text="error" style="display:none"></p>

    {{-- Hint text --}}
    @if($hint)
        <p class="text-[10px] text-primary/40 mt-1 leading-tight" x-show="!error">{{ $hint }}</p>
    @endif

    {{-- Character counter for maxlength fields --}}
    @if($maxlength)
        <span class="absolute right-0 -bottom-4 text-[10px] text-primary/30" x-data
              x-text="'{{ $maxlength - strlen($value) }} left'"></span>
    @endif
</div>

@once
@push('scripts')
<script>
function validate(event, type, required) {
    const input = event.target;
    const val   = input.value.trim();
    const comp  = input.closest('[x-data]').__x;

    if (!comp) return;

    let msg = '';

    if (required && !val) {
        msg = 'This field is required.';
    } else if (val) {
        if (type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
            msg = 'Enter a valid email address.';
        } else if (type === 'tel' && !/^[+\d\s\-().]{7,20}$/.test(val)) {
            msg = 'Enter a valid phone number.';
        } else if (type === 'url' && !/^https?:\/\/.+/.test(val)) {
            msg = 'Enter a valid URL (starting with http:// or https://).';
        }
    }

    comp.$data.error = msg;
}
</script>
@endpush
@endonce