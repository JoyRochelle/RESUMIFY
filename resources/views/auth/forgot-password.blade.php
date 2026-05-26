@extends('layouts.auth.centered')

@section('title', 'Forgot Password | Resumify')

@section('auth-content')
    <div class="text-center mb-8">
        <h2 class="text-2xl font-headline font-bold text-on-surface">Forgot Password</h2>
        <p class="text-sm text-on-surface-variant mt-2">
            Enter your email and we'll send you a link to reset your password.
        </p>
    </div>

    @if (session('status'))
        <x-auth.alert type="success">
            {{ session('status') }}
        </x-auth.alert>
    @endif


    <x-auth.error-list />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6" x-data="{ loading: false }"
        @submit="loading = true">
        @csrf
        <x-auth.input name="email" label="EMAIL ADDRESS" type="email" placeholder="name@email.com" required autofocus />

        <x-auth.button>Send Password Reset Link</x-auth.button>
    </form>

    <div class="text-center mt-8">
        <a href="{{ route('login') }}" class="text-sm font-bold text-secondary hover:underline">
            Back to Login
        </a>
    </div>
@endsection
