@extends('layouts.app')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="col-md-6">
        <div class="card shadow-lg border-0" style="border-radius: 12px;">

            <div class="card-header text-center bg-white border-0" style="font-size: 20px; font-weight: 600;">
                {{ __('Verify Your Email') }}
            </div>

            <div class="card-body text-center px-4 py-4">

                @if (session('resent'))
                <div class="alert alert-success">
                    {{ __('Verification link sent!') }}
                </div>
                @endif

                <p class="mb-3">
                    {{ __('Please check your email for verification link.') }}
                </p>

                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-link">
                        {{ __('Resend Email') }}
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
@endsection