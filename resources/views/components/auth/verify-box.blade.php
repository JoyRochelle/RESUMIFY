<div class="flex flex-col items-center text-center py-4 animate-in fade-in zoom-in duration-500">
    {{-- Success Icon --}}
    <div class="w-16 h-16 bg-secondary/10 rounded-2xl flex items-center justify-center mb-8">
        <div class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center shadow-lg shadow-secondary/30">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
    </div>

    {{-- Text Content --}}
    <h2 class="text-3xl font-headline font-bold text-primary mb-4 leading-tight">
        {!! __('messages.auth.verify_email.heading') !!}
    </h2>
    <p class="text-on-surface-variant text-sm leading-relaxed mb-8 max-w-[280px]">
        {{ __('messages.auth.verify_email.body') }}
    </p>

    {{-- Primary Action (Logout to return to Login) --}}
    <form method="POST" action="{{ route('logout') }}" class="w-full">
        @csrf
        <button type="submit"
            class="w-full bg-primary text-white py-4 rounded-xl font-bold shadow-xl shadow-primary/20 hover:opacity-90 active:scale-[0.98] transition-all mb-6 cursor-pointer">
            {{ __('messages.auth.verify_email.back_to_login') }}
        </button>
    </form>

    {{-- Secondary Action --}}
    <div class="space-y-4">
        <p class="text-xs text-on-surface-variant/60 font-medium uppercase tracking-widest">
            {{ __('messages.auth.verify_email.no_email_question') }}
        </p>
        <form method="POST" action="{{ route('verification.send') }}" x-data="{ loading: false }" @submit="loading = true">
            @csrf
            <button type="submit" :disabled="loading"
                class="text-secondary font-bold hover:underline transition-all disabled:opacity-50"
                x-text="loading ? '{{ __('messages.auth.verify_email.sending') }}' : '{{ __('messages.auth.verify_email.resend') }}'">
            </button>
        </form>
    </div>

    {{-- Decorative Dots --}}
    <div class="flex gap-2 mt-12">
        <div class="w-8 h-1.5 bg-primary rounded-full"></div>
        <div class="w-3 h-1.5 bg-outline-variant/30 rounded-full"></div>
        <div class="w-3 h-1.5 bg-outline-variant/30 rounded-full"></div>
    </div>
</div>
