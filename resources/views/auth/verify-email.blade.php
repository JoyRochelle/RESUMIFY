@extends('layouts.auth.centered')

@section('title', __('messages.auth.verify_email.title'))
@section('auth-content')
    <x-auth.verify-box />
@endsection
