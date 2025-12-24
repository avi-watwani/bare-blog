@extends('layouts.guest')

@section('content')
    <h1 class="h4 fw-bold mb-3">Verify Email</h1>
    <p class="text-muted mb-3">
        Thanks for signing up! Please verify your email by clicking the link we sent. Need another link? Request below.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            A new verification link has been sent to your email.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mt-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Resend Verification Email</button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-link text-secondary text-decoration-none">Log Out</button>
        </form>
    </div>
@endsection
