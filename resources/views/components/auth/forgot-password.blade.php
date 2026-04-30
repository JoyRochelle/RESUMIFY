<div class="bg-surface-container-lowest rounded-2xl shadow-2xl border border-outline-variant/30 p-8">
    {{-- header --}}
    <div class="text-center mb-8">
        <h2 class="text-3xl font-headline font-bold text-on-surface">
            Forgot Password?
        </h2>
        <p class="text-sm text-on-surface-variant mt-3 leading-relaxed">
            Enter your email address and we'll send you a link to reset your password.
        </p>
    </div>

    {{-- Feedback Messages --}}
    @if (session('status'))
        <div
            class="mb-4 p-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-xl font-medium flex items-center gap-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"></path>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    {{-- Validation Error (Khusus forgot password) --}}
    <x-auth.error-list :errors="$errors" class="mb-4" />

    {{-- form --}}
    <form action="{{ route('password.email') }}" method="POST" class="space-y-6" x-data="{ loading: false }"
        @submit="loading = true">
        @csrf
        <input type="hidden" name="from_forgot_password" value="1">

        <x-auth.input name="email" label="EMAIL ADDRESS" type="email" placeholder="Enter your email" required />

        <x-auth.button>Send Link</x-auth.button>
    </form>

    {{-- footer --}}
    <div class="text-center mt-6">
        <button @click="showForgotModal = false"
            class="inline-flex gap-2 text-sm font-bold text-secondary hover:text-secondary/70 transition-colors">
            Back to Login
        </button>
    </div>
</div>
