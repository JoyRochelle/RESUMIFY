@props(['surface' => 'light'])

@php
    $current = app()->getLocale();
    $locales = ['en' => 'EN', 'id' => 'ID'];
    $toneClasses = $surface === 'dark'
        ? ['active' => 'bg-white/15 text-white', 'inactive' => 'text-white/60 hover:text-white']
        : ['active' => 'bg-tertiary text-primary shadow-sm', 'inactive' => 'text-primary/50 hover:text-primary'];
@endphp

<div class="inline-flex items-center gap-0.5 rounded-lg border border-primary/10 p-0.5" role="group" aria-label="Language switcher">
    @foreach($locales as $code => $label)
        <form method="POST" action="{{ route('locale.update', $code) }}">
            @csrf
            <button type="submit"
                    class="min-h-8 rounded-md px-2.5 text-xs font-bold tracking-wide transition-colors focus:outline-none focus:ring-2 focus:ring-secondary/40 {{ $current === $code ? $toneClasses['active'] : $toneClasses['inactive'] }}"
                    @if($current === $code) aria-current="true" @endif
                    aria-label="{{ 'Switch language to ' . $label }}">
                {{ $label }}
            </button>
        </form>
    @endforeach
</div>
