@extends('layouts.auth.centered')

@section('title', __('messages.auth.reset_password.title'))

@section('auth-content')
    <x-auth.error-list />

    <form class="space-y-6" action="{{ route('password.update') }}" method="POST" x-data="{ loading: false }"
        @submit="loading = true">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ $request->email }}">

        <x-auth.input name="password" label="{{ __('messages.auth.reset_password.new_password') }}" type="password" placeholder="********" required />
        <x-auth.input name="password_confirmation" label="{{ __('messages.auth.reset_password.confirm_password') }}" type="password" placeholder="********"
            required />

        <x-auth.button>{{ __('messages.auth.reset_password.submit') }}</x-auth.button>
    </form>
@endsection
