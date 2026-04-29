@extends('layouts.user_dashboard.app')

@section('title', 'Dashboard - Resumify')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2>Dashboard</h2>
                    <p>Welcome, {{ Auth::user()->name }}!</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
