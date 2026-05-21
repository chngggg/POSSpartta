@extends('layouts.guest')

@section('title', 'Reset Password - SparttaPOS')

@section('content')
<style>
    .reset-page {
        min-height: 100vh;
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
        url('{{ asset("images/toko-viar.jpg") }}') no-repeat center center/cover;
        background-size: cover;
        background-position: center;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .reset-page::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        backdrop-filter: blur(8px);
        background: rgba(0, 0, 0, 0.3);
    }

    .reset-card {
        position: relative;
        z-index: 10;
        background: rgba(20, 20, 20, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        border: 1px solid rgba(212, 175, 55, 0.3);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        width: 100%;
        max-width: 450px;
        animation: fadeInUp 0.6s ease;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .reset-header {
        text-align: center;
        padding: 40px 32px 20px 32px;
        border-bottom: 1px solid rgba(212, 175, 55, 0.2);
    }

    .reset-header h1 {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(135deg, #d4af37 0%, #f5c542 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: 2px;
        margin-bottom: 8px;
    }

    .reset-body {
        padding: 32px;
    }

    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: #c0c0c0;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .form-group label i {
        color: #d4af37;
        margin-right: 8px;
        width: 20px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        background: #1a1a1a;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        color: #ffffff;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        outline: none;
        border-color: #d4af37;
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }

    .btn-reset {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, #d4af37, #b8942e);
        border: none;
        border-radius: 12px;
        color: #0a0a0a;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 8px;
    }

    .btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(212, 175, 55, 0.3);
    }

    .back-link {
        text-align: center;
        margin-top: 20px;
    }

    .back-link a {
        color: #b0b0b0;
        text-decoration: none;
        font-size: 0.85rem;
        transition: color 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .back-link a:hover {
        color: #d4af37;
    }

    .alert-danger {
        background: rgba(231, 76, 60, 0.1);
        border: 1px solid rgba(231, 76, 60, 0.3);
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 24px;
        color: #e74c3c;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    @media (max-width: 768px) {
        .reset-card {
            margin: 20px;
            max-width: calc(100% - 40px);
        }

        .reset-header {
            padding: 30px 24px 16px 24px;
        }

        .reset-header h1 {
            font-size: 1.6rem;
        }

        .reset-body {
            padding: 24px;
        }
    }
</style>

<div class="reset-page">
    <div class="reset-card">
        <div class="reset-header">
            <h1>Reset Password</h1>
        </div>

        <div class="reset-body">
            @if ($errors->any())
            <div class="alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope"></i> Email Address
                    </label>
                    <input id="email" type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        name="email" value="{{ $email ?? old('email') }}"
                        placeholder="masukkan@email.com"
                        required>
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password Baru
                    </label>
                    <input id="password" type="password"
                        class="form-control @error('password') is-invalid @enderror"
                        name="password"
                        placeholder="Minimal 6 karakter"
                        required>
                </div>

                <div class="form-group">
                    <label for="password-confirm">
                        <i class="fas fa-check-circle"></i> Konfirmasi Password
                    </label>
                    <input id="password-confirm" type="password"
                        class="form-control"
                        name="password_confirmation"
                        placeholder="Ulangi password baru"
                        required>
                </div>

                <button type="submit" class="btn-reset">
                    <i class="fas fa-save me-2"></i> Reset Password
                </button>

                <div class="back-link">
                    <a href="{{ route('login') }}">
                        <i class="fas fa-arrow-left"></i>
                        Kembali ke Halaman Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection