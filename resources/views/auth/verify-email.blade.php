<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email | Resumify</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class='min-h-screen bg-surface flex items-center justify-center p-6'>
    <div
        class="w-full max-w-md bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-8 md:p-12 shadow-2xl shadow-primary/5 transition-auth-card">
        {{-- Brand Anchor --}}
        <div class="text-center mb-10">
            <h2 class="text-4xl font-headline font-bold text-primary tracking-tighter">Resumify</h2>
            <div class="h-1 w-8 bg-secondary mx-auto mt-1 rounded-full"></div>
        </div>

        {{-- The Verify Box Component --}}
        <x-auth.verify-box />
    </div>
</body>

</html>
