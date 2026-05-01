@extends('layouts.master')

@section('title', 'Edit Kategori')

@section('content')
<div class="category-form-container">
    <div class="form-header">
        <a href="{{ route('categories.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4>
                <i class="fas fa-edit me-2"></i>
                Edit Kategori
            </h4>
            <p class="text-muted">Edit informasi kategori {{ $category->name }}</p>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('categories.update', $category) }}" method="POST" id="categoryForm">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">
                    <i class="fas fa-tag"></i>
                    Nama Kategori <span class="text-danger">*</span>
                </label>
                <input type="text"
                    class="form-control @error('name') is-invalid @enderror"
                    id="name"
                    name="name"
                    value="{{ old('name', $category->name) }}"
                    placeholder="Contoh: Mesin, Kelistrikan, Body"
                    required>
                <small class="text-muted">Nama kategori akan otomatis dibuatkan slug</small>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="slug">
                    <i class="fas fa-link"></i>
                    Slug
                </label>
                <input type="text"
                    class="form-control"
                    id="slug"
                    name="slug"
                    value="{{ old('slug', $category->slug) }}"
                    readonly>
                <small class="text-muted">Slug digunakan untuk URL friendly</small>
            </div>

            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i>
                    Deskripsi
                </label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                    id="description"
                    name="description"
                    rows="4"
                    placeholder="Deskripsi kategori (opsional)">{{ old('description', $category->description) }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="is_active">
                    <i class="fas fa-toggle-on"></i>
                    Status
                </label>
                <select class="form-control @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                    <option value="1" {{ old('is_active', $category->is_active) == 1 ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ old('is_active', $category->is_active) == 0 ? 'selected' : '' }}>Nonaktif</option>
                </select>
                <small class="text-muted">Kategori nonaktif tidak akan ditampilkan di beberapa menu</small>
                @error('is_active')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-save me-2"></i>
                    Update Kategori
                </button>
                <a href="{{ route('categories.index') }}" class="btn btn-outline-gold">
                    <i class="fas fa-times me-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/category.css') }}">
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');

        if (nameInput && slugInput) {
            nameInput.addEventListener('keyup', function() {
                const name = this.value;
                const slug = name.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                slugInput.value = slug;
            });
        }
    });
</script>
@endpush