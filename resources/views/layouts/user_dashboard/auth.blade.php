<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'Resumify' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="view-transition" content="same-origin">
    {{ $head ?? '' }}
</head>

<body {{ $attributes->merge(['class' => 'min-h-screen bg-surface selection:bg-secondary/30 selection:text-primary']) }}>
    {{ $slot }}
</body>

</html>
