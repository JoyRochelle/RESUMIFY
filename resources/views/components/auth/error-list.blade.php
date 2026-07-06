@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'mb-6 p-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-lg font-medium leading-relaxed']) }}
         role="alert"
         aria-live="assertive"
         tabindex="-1">
        <p class="font-bold">{{ __('messages.auth.error_summary') }}</p>
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
