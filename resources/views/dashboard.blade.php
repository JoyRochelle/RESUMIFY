@extends('layouts.app')

@section('title', 'Dashboard - Resumify')

@section('content')
    <div class="flex justify-center">
        <div class="w-full max-w-2xl">
            <div class="bg-tertiary rounded-2xl border border-primary/10 shadow-sm">
                <div class="p-8">
                    <h2 class="font-headline text-2xl font-bold text-primary mb-2">Dashboard</h2>
                    <p class="text-primary/70 font-body mb-6">Welcome, {{ Auth::user()->name }}!</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="px-5 py-2 text-sm font-bold text-red-600 border border-red-300 rounded-lg hover:bg-red-600 hover:text-white transition-all">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
