<x-layouts.auth title="Register | Resumify">
    <main class="flex min-h-screen md:h-screen flex-col md:flex-row">
        {{-- bagian kiri --}}
        <x-auth.left-section />

        {{-- bagian kanan --}}
        <x-auth.right-section>
            <x-slot name="title">Create Your Account</x-slot>
            <x-slot name="subtitle">Start your professional journey in minutes.</x-slot>

            <x-auth.error-list />

            <form class="space-y-4" action="{{ route('register') }}" method="POST" x-data="{ loading: false }"
                @submit="loading = true">
                @csrf
                <x-auth.input name="name" label="FULL NAME" placeholder="John Doe" required autofocus />

                <x-auth.input name="email" label="EMAIL" type="email" placeholder="name@email.com" required />

                <div class="grid grid-cols-2 gap-4">
                    <x-auth.input name="password" label="PASSWORD" type="password" placeholder="••••••••" required />
                    <x-auth.input name="password_confirmation" label="CONFIRM" type="password" placeholder="••••••••"
                        required />
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

                <x-auth.button>Sign Up</x-auth.button>
            </form>

            <x-slot name="footer">
                <p class="text-sm text-on-surface-variant">
                    Already have an account?
                    <a class="text-secondary font-bold hover:underline" href="{{ route('login') }}">Sign In</a>
                </p>
            </x-slot>
        </x-auth.right-section>
    </main>
</x-layouts.auth>
