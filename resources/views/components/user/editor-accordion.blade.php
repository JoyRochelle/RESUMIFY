@props(['title', 'icon', 'isOpen' => false])

<x-ui.disclosure :title="$title" :icon="$icon" :open="$isOpen" {{ $attributes }}>
    <div class="space-y-8">
        {{ $slot }}
    </div>
</x-ui.disclosure>
