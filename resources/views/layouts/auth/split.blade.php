@extends('layouts.auth.master')

@section('content')
    <main class="flex min-h-screen md:h-screen flex-col md:flex-row">
        {{-- left section : branding/image --}}
        <x-auth.left-section />

        {{-- right section : auth form --}}
        <x-auth.right-section>
            <x-slot name="title">@yield('auth-title')</x-slot>
            <x-slot name="subtitle">@yield('auth-subtitle')</x-slot>

            @yield('auth-form')
            <x-slot name="footer">
                @yield('auth-footer')
            </x-slot>
        </x-auth.right-section>
    </main>
@endsection
