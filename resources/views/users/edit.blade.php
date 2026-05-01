@extends('layouts.master')

@section('title', 'Edit User')

@section('content')
<div class="user-form-container">
    <div class="form-header">
        <a href="{{ route('users.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4>
                <i class="fas fa-user-edit me-2"></i>
                Edit User
            </h4>
            <p class="text-muted">Edit informasi user {{ $user->name }}</p>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('users.update', $user) }}" method="POST" id="userForm">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">
                    <i class="fas fa-user"></i>
                    Nama Lengkap <span class="text-danger">*</span>
                </label>
                <input type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="name"
                    name="name"
                    value="{{ old('name', $user->name) }}"
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
                    value="{{ old('email', $user->email) }}"
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
                            Password Baru
                        </label>
                        <input type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Kosongkan jika tidak ingin mengubah">
                        <small class="text-muted">Minimal 6 karakter</small>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="password_confirmation">
                            <i class="fas fa-check-circle"></i>
                            Konfirmasi Password
                        </label>
                        <input type="password"
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password baru">
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
                    <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
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
                    Update User
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