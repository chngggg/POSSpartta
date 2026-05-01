@extends('layouts.master')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Pengaturan Profile</h4>
        </div>

        <div class="card-body">
            <p class="text-muted">Halaman pengaturan akun pengguna.</p>

            <div class="mb-3">
                <label>Nama</label>
                <input type="text" class="form-control" value="{{ auth()->user()->name }}">
            </div>

            <div class="mb-3">
                <label>Email</label>
                <input type="email" class="form-control" value="{{ auth()->user()->email }}">
            </div>

            <button class="btn btn-primary">Simpan Perubahan</button>
        </div>
    </div>
</div>
@endsection