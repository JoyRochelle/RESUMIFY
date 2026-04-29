@extends('layouts.auth.master')

{{-- Use the body-class to center everything --}}
@section('body-class', 'flex items-center justify-center p-6')

@section('content')
    <div class="w-full max-w-md bg-surface-container-lowest rounded-3xl border border-outline-variant/30 p-8 md:p-12 shadow-2xl shadow-primary/5 transition-auth-card">
        {{-- Brand Anchor --}}
        <x-auth.brand class="mb-10" />

        <div class="mt-8">
            @yield('auth-content')
        </div>
    </div>
@endsection
