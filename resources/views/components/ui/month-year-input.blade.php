@props([
    'label',
    'name'     => '',
    'value'    => '',
    'hint'     => '',
    'required' => false,
])

@php
    // Stored format stays exactly what <input type="month"> produced: YYYY-MM.
    // Every resume template renders this value raw, so changing it would mean
    // migrating existing rows and 23 blade templates.
    $selectedYear = '';
    $selectedMonth = '';
    if (preg_match('/^(\d{4})-(\d{2})$/', (string) $value, $matches)) {
        $selectedYear  = $matches[1];
        $selectedMonth = $matches[2];
    }

    $currentYear = (int) date('Y');
    $years = range($currentYear + 5, $currentYear - 60);

    // A saved year outside the range must stay selectable, or opening the
    // editor would silently drop the user's date.
    if ($selectedYear !== '' && !in_array((int) $selectedYear, $years, true)) {
        $years[] = (int) $selectedYear;
        rsort($years);
    }

    // Month names are abbreviated: the editor panel gives each date field about
    // 160px to split between two selects, and a full "September" is clipped at
    // that width — worse still at 375px. The year, which is what users actually
    // struggled to reach, keeps its full four digits.
    $months = __('messages.editor.date_picker.months');
    // pr-5 keeps the label clear of the native dropdown arrow.
    $selectClasses = 'w-full border-b-2 border-primary/15 focus:border-secondary bg-transparent py-2 pl-0 pr-5 outline-none transition-all duration-200 focus:ring-0 text-primary text-sm';
@endphp

<div class="relative group/input" data-month-year>
    <span class="text-[11px] font-bold uppercase tracking-wider text-primary/60 mb-1 flex items-center gap-1">
        {{ $label }}
        @if($required)
            <span class="text-red-400 text-[10px]" aria-hidden="true">*</span>
        @endif
    </span>

    {{-- The selects deliberately carry no name: saveSection() sweeps every
         named input, textarea and select in the row into the payload, so a
         named select would write stray keys into the resume content. --}}
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" {{ $attributes->merge(['class' => 'auto-save js-month-year-value']) }}>

    <div class="grid grid-cols-2 gap-2">
        <label>
            <span class="sr-only">{{ $label }} — {{ __('messages.editor.date_picker.year') }}</span>
            <select data-month-year-year onchange="syncMonthYearInput(this)" class="{{ $selectClasses }}">
                <option value="">{{ __('messages.editor.date_picker.year') }}</option>
                @foreach($years as $year)
                    <option value="{{ $year }}" @selected((string) $year === $selectedYear)>{{ $year }}</option>
                @endforeach
            </select>
        </label>

        <label>
            <span class="sr-only">{{ $label }} — {{ __('messages.editor.date_picker.month') }}</span>
            <select data-month-year-month onchange="syncMonthYearInput(this)" class="{{ $selectClasses }}">
                <option value="">{{ __('messages.editor.date_picker.month') }}</option>
                @foreach($months as $number => $monthName)
                    <option value="{{ $number }}" @selected($number === $selectedMonth)>{{ $monthName }}</option>
                @endforeach
            </select>
        </label>
    </div>

    @if($hint)
        <p class="text-[10px] text-primary/40 mt-1 leading-tight">{{ $hint }}</p>
    @endif
</div>

@once
@push('scripts')
<script>
    function syncMonthYearInput(select) {
        const root = select.closest('[data-month-year]');
        if (!root) return;

        const hidden = root.querySelector('.js-month-year-value');
        const year = root.querySelector('[data-month-year-year]')?.value || '';
        const month = root.querySelector('[data-month-year-month]')?.value || '';
        if (!hidden) return;

        // Half a date is not a date — never store "2020-" or "-05".
        hidden.value = (year && month) ? `${year}-${month}` : '';

        // The editor's auto-save listens for input events on INPUT elements;
        // a hidden field set from script fires nothing on its own.
        hidden.dispatchEvent(new Event('input', { bubbles: true }));
    }
</script>
@endpush
@endonce
