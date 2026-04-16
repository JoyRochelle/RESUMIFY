@extends('layouts.app')

@section('title', 'Verify Email - Resumify')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="card-title text-center mb-3">Verify Your Email</h2>

                    <p class="text-center text-muted">
                        Thanks for signing up! Before getting started, please verify your email address
                        by clicking the link we just sent to you.
                    </p>

                    @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success">
                            A new verification link has been sent to your email address.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">Resend Verification Email</button>
                    </form>

                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary w-100">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
