<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register | Resumify</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="view-transition" content="same-origin">
</head>

<body class='min-h-screen bg-surface selection:bg-secondary/30 selection:text-primary'>
    <main class="flex min-h-screen md:h-screen flex-col md:flex-row">
        {{-- bagian kiri --}}
        <x-auth.left-section />

        {{-- bagian kanan --}}
        <x-auth.right-section>
            <x-slot name="title">Create Your Account</x-slot>
            <x-slot name="subtitle">Start your professional journey in minutes.</x-slot>

            @if ($errors->any())
                <div
                    class="mb-6 p-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-xl font-medium leading-relaxed">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-4" action="{{ route('register') }}" method="POST" x-data="{ loading: false }"
                @submit="loading = true">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant"
                        for="name">FULL NAME</label>
                    <input
                        class="w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all placeholder:text-outline/50"
                        id="name" name="name" placeholder="John Doe" type="text" required autofocus
                        value="{{ old('name') }}" />
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant"
                        for="email">EMAIL</label>
                    <input
                        class="w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all placeholder:text-outline/50"
                        id="email" name="email" placeholder="name@email.com" type="email" required
                        value="{{ old('email') }}" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant"
                            for="password">PASSWORD</label>
                        <input
                            class="w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all placeholder:text-outline/50"
                            id="password" name="password" placeholder="••••••••" type="password" required />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant"
                            for="password_confirmation">CONFIRM</label>
                        <input
                            class="w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all placeholder:text-outline/50"
                            id="password_confirmation" name="password_confirmation" placeholder="••••••••"
                            type="password" required />
                    </div>
                </div>

                <div class="flex items-start gap-3 py-2">
                    <input class="mt-1 w-4 h-4 rounded border-outline-variant text-secondary focus:ring-secondary"
                        id="terms" name="terms" type="checkbox" required />
                    <label class="text-xs text-on-surface-variant leading-relaxed" for="terms">
                        I agree to the <a href="#" class="text-secondary font-bold hover:underline">Terms of
                            Service</a> and <a href="#" class="text-secondary font-bold hover:underline">Privacy
                            Policy</a>.
                    </label>
                </div>

                <button type="submit" :disabled="loading" {{-- Mematikan tombol saat loading --}}
                    :class="loading ? 'opacity-70 cursor-not-allowed' : 'hover:opacity-90'" {{-- Feedback visual --}}
                    class="w-full bg-primary text-white py-3.5 rounded-lg font-bold transition-all flex items-center justify-center gap-2">
                    <span x-text="loading ? 'Processing...' : 'Sign Up'"></span>
                </button>

            </form>

            <x-slot name="footer">
                <p class="text-sm text-on-surface-variant">
                    Already have an account?
                    <a class="text-secondary font-bold hover:underline" href="{{ route('login') }}">Sign In</a>
                </p>
            </x-slot>
        </x-auth.right-section>
    </main>
</body>

</html>
