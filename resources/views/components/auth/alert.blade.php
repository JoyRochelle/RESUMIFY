@props(['type' => 'success'])

@php
    $styles =
        [
            'success' => 'bg-green-50 text-green-700 border-green-200',
            'error' => 'bg-red-50 text-red-700 border-red-200',
            'info' => 'bg-blue-50 text-blue-700 border-blue-200',
        ][$type] ?? $styles['success'];
@endphp

<div {{ $attributes->merge(['class' => "mb-6 p-4 rounded-xl border text-sm font-medium $styles"]) }}>
    {{ $slot }}
</div>
