<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Resumify</title>
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

        {{-- The Reset Password Form --}}
        <div class="mt-8">
            @if ($errors->any())
                <div class="mb-4 p-4 text-sm text-red-700 bg-red-50 border border-red-200 rounded-xl font-medium">
                    {{ $errors->first() }}
                </div>
            @endif

            <form class="space-y-3" action="{{ route('password.update') }}" method="POST" x-data="{ loading: false }"
                @submit="loading = true">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input type="hidden" name="email" value="{{ $request->email }}">
                <div class="space-y-2">
                    <label class="font-bold uppercase tracking-wider text-on-surface-variant" for="password">New
                        Password</label>
                    <input
                        class="w-full px-4 py-3.5 rounded-xl border border-outline-variant bg-surface focus:ring-2 outline-none transition-all placeholder:text-outline/50"
                        name="password" id="password" type="password" placeholder="********" required>
                </div>
                <div class="space-y-2">
                    <label class="font-bold uppercase tracking-wider text-on-surface-variant"
                        for="password-confirmation">Confirm
                        Password</label>
                    <input
                        class="w-full px-4 py-3.5 rounded-xl border border-outline-variant bg-surface focus:ring-2 outline-none transition-all placeholder:text-outline/50"
                        id="password_confirmation" name="password_confirmation" type="password" placeholder="********"
                        required>
                </div>
                <button type="submit" :disabled="loading"
                    :class="loading ? 'opacity-70 cursor-not-allowed' : 'hover:opacity-90'"
                    class="w-full bg-primary text-white py-3.5 rounded-lg font-bold transition-all flex items-center justify-center gap-2">
                    <span x-text="loading ? 'Processing...' : 'Update Password'"></span>
                </button>

            </form>
        </div>
    </div>
</body>

</html>
