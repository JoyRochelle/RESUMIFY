<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login | Resumify</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta name="view-transition" content="same-origin">
</head>

<body x-data="{ showForgotModal: {{ old('from_forgot_password') || session('status') ? 'true' : 'false' }} }" class='min-h-screen bg-surface selection:bg-secondary/30 selection:text-primary'>
    <main class="flex min-h-screen md:h-screen flex-col md:flex-row">
        {{-- bagian kiri --}}
        <x-auth.left-section />

        {{-- bagian kanan --}}
        <x-auth.right-section>
            {{-- Tampilkan Semua Error Validasi --}}
            @if ($errors->any() && !old('from_forgot_password'))
                <div
                    class="mb-6 p-4 text-sm text-red-800 bg-red-50 border border-red-200 rounded-xl font-medium leading-relaxed">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form class="space-y-5" action="{{ route('login') }}" method="POST" x-data="{ loading: false }"
                @submit="loading = true">
                @csrf
                <div class="space-y-1.5">
                    <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant"
                        for="email">EMAIL</label>
                    <input
                        class="w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all placeholder:text-outline/50"
                        id="email" name="email" placeholder="name@email.com" type="email" required
                        value="{{ old('email') }}" />
                </div>

                <div class="space-y-1.5">
                    <div class="flex justify-between items-center">
                        <label class="text-xs font-bold uppercase tracking-wider text-on-surface-variant"
                            for="password">PASSWORD</label>
                        <a @click.prevent="showForgotModal = true"
                            class="text-xs font-bold text-secondary hover:underline" href="#">Forgot
                            Password?</a>
                    </div>
                    <input
                        class="w-full px-4 py-3 rounded-lg border border-outline-variant bg-surface focus:ring-2 focus:ring-secondary/20 focus:border-secondary outline-none transition-all placeholder:text-outline/50"
                        id="password" name="password" placeholder="••••••••" type="password" required />
                </div>

                <div class="flex items-center gap-3">
                    <input class="w-4 h-4 rounded border-outline-variant text-secondary focus:ring-secondary"
                        id="remember" name="remember" type="checkbox" />
                    <label class="text-sm text-on-surface-variant" for="remember">Remember me for 30
                        days</label>
                </div>

                <button type="submit" :disabled="loading" :class="loading ? 'opacity-70 cursor-not-allowed' : ''"
                    class="w-full bg-primary text-white py-3.5 rounded-lg font-bold hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                    <span x-text="loading ? 'Processing...' : 'Login'"></span>
                </button>
            </form>

            <x-slot name="footer">
                <p class="text-sm text-on-surface-variant">
                    Don't have an account?
                    <a class="text-secondary font-bold hover:underline" href="{{ route('register') }}">Sign Up Free</a>
                </p>
            </x-slot>
        </x-auth.right-section>
    </main>

    <div x-show="showForgotModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        style="display: none;">

        <div @click.away="showForgotModal = false" class="w-full max-w-md">
            <x-auth.forgot-password />
        </div>
    </div>

</body>

</html>
