@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'mb-6 p-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-xl font-medium leading-relaxed']) }}>
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
