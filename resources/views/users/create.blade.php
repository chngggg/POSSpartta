@extends('layouts.master')

@section('title', 'Tambah User')

@section('content')
<div class="user-form-container">
    <div class="form-header">
        <a href="{{ route('users.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4>
                <i class="fas fa-user-plus me-2"></i>
                Tambah User Baru
            </h4>
            <p class="text-muted">Isi formulir berikut untuk menambahkan user baru ke sistem</p>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('users.store') }}" method="POST" id="userForm">
            @csrf

            <div class="form-group">
                <label for="name">
                    <i class="fas fa-user"></i>
                    Nama Lengkap <span class="text-danger">*</span>
                </label>
                <input type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Masukkan nama lengkap"
                    required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i>
                    Email <span class="text-danger">*</span>
                </label>
                <input type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="contoh@email.com"
                    required>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password">
                            <i class="fas fa-lock"></i>
                            Password <span class="text-danger">*</span>
                        </label>
                        <input type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Minimal 6 karakter"
                            required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password_confirmation">
                            <i class="fas fa-check-circle"></i>
                            Konfirmasi Password <span class="text-danger">*</span>
                        </label>
                        <input type="password"
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="role_id">
                    <i class="fas fa-tag"></i>
                    Role / Hak Akses <span class="text-danger">*</span>
                </label>
                <select class="form-control @error('role_id') is-invalid @enderror"
                    id="role_id"
                    name="role_id"
                    required>
                    <option value="">Pilih Role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }} - {{ $role->description }}
                    </option>
                    @endforeach
                </select>
                @error('role_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-save me-2"></i>
                    Simpan User
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-gold">
                    <i class="fas fa-times me-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/user-management.css') }}">
@endpush

@extends('layouts.master')