<x-layouts.auth title="Reset Password | Resumify" class="flex items-center justify-center p-6">
    <div
        class="w-full max-w-md bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-8 md:p-12 shadow-2xl shadow-primary/5 transition-auth-card">
        {{-- Brand Anchor --}}
        <x-auth.brand class="mb-10" />

        {{-- The Reset Password Form --}}
        <div class="mt-8">
            <x-auth.error-list />

            <form class="space-y-6" action="{{ route('password.update') }}" method="POST" x-data="{ loading: false }"
                @submit="loading = true">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input type="hidden" name="email" value="{{ $request->email }}">

                <x-auth.input name="password" label="New Password" type="password" placeholder="********" required />
                <x-auth.input name="password_confirmation" label="Confirm Password" type="password"
                    placeholder="********" required />

                <x-auth.button>Update Password</x-auth.button>
            </form>
        </div>
    </div>
</x-layouts.auth>
