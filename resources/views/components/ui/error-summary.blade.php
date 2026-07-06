@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'mb-6 rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800']) }}
         role="alert"
         aria-live="assertive"
         tabindex="-1">
        <p class="font-bold">{{ __('messages.common.fix_following_fields') }}</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
