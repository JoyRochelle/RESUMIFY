@extends('layouts.auth.split')

@section('title', __('messages.auth.register.title'))

@section('auth-title', __('messages.auth.register.heading'))
@section('auth-subtitle', __('messages.auth.register.subtitle'))

@section('auth-form')
    <x-auth.error-list />

    <form class="space-y-4" action="{{ route('register') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
        @csrf
        <x-auth.input name="name" label="{{ __('messages.auth.fields.full_name') }}" placeholder="John Doe" required autofocus />

        <x-auth.input name="email" label="{{ __('messages.auth.fields.email') }}" type="email" placeholder="name@email.com" required />

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <x-auth.input name="password" label="{{ __('messages.auth.fields.password') }}" type="password" placeholder="••••••••" required />
            <x-auth.input name="password_confirmation" label="{{ __('messages.auth.fields.confirm') }}" type="password" placeholder="••••••••" required />
        </div>

        <div class="flex items-start gap-3 py-2">
            <input class="mt-1 w-4 h-4 rounded border-outline-variant text-secondary focus:ring-secondary" id="terms"
                name="terms" type="checkbox" required />
            <label class="text-xs text-on-surface-variant leading-relaxed" for="terms">
                {{ __('messages.auth.register.terms_prefix') }}
                <button type="button" @click.stop.prevent="openModal('terms')" class="text-secondary font-bold hover:underline">
                    {{ __('messages.auth.register.terms_of_service') }}
                </button>
                {{ __('messages.auth.register.and') }}
                <button type="button" @click.stop.prevent="openModal('privacy')" class="text-secondary font-bold hover:underline">
                    {{ __('messages.auth.register.privacy_policy') }}
                </button>.
            </label>
        </div>

        <x-auth.button>{{ __('messages.auth.register.submit') }}</x-auth.button>
    </form>
@endsection

@section('auth-footer')
    <p class="text-sm text-on-surface-variant">
        {{ __('messages.auth.register.have_account') }}
        <a class="text-secondary font-bold hover:underline" href="{{ route('login') }}">{{ __('messages.auth.register.sign_in') }}</a>
    </p>
@endsection
