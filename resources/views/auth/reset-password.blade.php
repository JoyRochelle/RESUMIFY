@extends('layouts.auth.centered')

@section('title', 'Reset Password | Resumify')

@section('auth-content')
    <x-auth.error-list />

    <form class="space-y-6" action="{{ route('password.update') }}" method="POST" x-data="{ loading: false }"
        @submit="loading = true">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <input type="hidden" name="email" value="{{ $request->email }}">

        <x-auth.input name="password" label="New Password" type="password" placeholder="********" required />
        <x-auth.input name="password_confirmation" label="Confirm Password" type="password" placeholder="********"
            required />

        <x-auth.button>Update Password</x-auth.button>
    </form>
@endsection
