@extends('layouts.auth.centered')

@section('title', __('messages.auth.forgot_password.title'))

@section('auth-content')
    <div class="text-center mb-8">
        <h2 class="text-2xl font-headline font-bold text-on-surface">{{ __('messages.auth.forgot_password.heading') }}</h2>
        <p class="text-sm text-on-surface-variant mt-2">
            {{ __('messages.auth.forgot_password.subtitle') }}
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
        <x-auth.input name="email" label="{{ __('messages.auth.fields.email_address') }}" type="email" placeholder="name@email.com" required autofocus />

        <x-auth.button>{{ __('messages.auth.forgot_password.submit') }}</x-auth.button>
    </form>

    <div class="text-center mt-8">
        <a href="{{ route('login') }}" class="text-sm font-bold text-secondary hover:underline">
            {{ __('messages.auth.forgot_password.back_to_login') }}
        </a>
    </div>
@endsection
