@extends('layouts.auth.split')

@section('title', 'Login | Resumify')


{{-- Alpine.js state for the forgot password modal --}}
@section('body-attributes')
    x-data="{ showForgotModal: {{ old('from_forgot_password') || session('status') ? 'true' : 'false' }} }"
@endsection

@section('auth-title', 'Login to Your Account')
@section('auth-subtitle', 'Welcome back to your career journey.')

@section('auth-form')
    <x-auth.error-list />

    <form class="space-y-5" action="{{ route('login') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
        @csrf
        <x-auth.input name="email" label="EMAIL" type="email" placeholder="name@email.com" required />

        <x-auth.input name="password" label="PASSWORD" type="password" placeholder="••••••••" required>
            <x-slot name="extraLabel">
                <a @click.prevent="showForgotModal = true" class="text-xs font-bold text-secondary hover:underline"
                    href="#">
                    Forgot Password?
                </a>
            </x-slot>
        </x-auth.input>

        <div class="flex items-center gap-3">
            <input class="w-4 h-4 rounded border-outline-variant text-secondary focus:ring-secondary" id="remember"
                name="remember" type="checkbox" />
            <label class="text-sm text-on-surface-variant" for="remember">Remember me for 30
                days</label>
        </div>

        <x-auth.button>Login</x-auth.button>
    </form>
@endsection

@section('auth-footer')
    {{-- Forgot Password Modal --}}
    <div x-show="showForgotModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm" style="display: none;">

        <div @click.away="showForgotModal = false" class="w-full max-w-md">
            <x-auth.forgot-password />
        </div>
    </div>
@endsection