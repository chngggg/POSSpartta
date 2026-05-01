@extends('layouts.master')

@section('title', 'Tambah Sparepart')

@section('content')
<div class="sparepart-form-container">
    <!-- Header -->
    <div class="form-header">
        <a href="{{ route('spareparts.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4>
                <i class="fas fa-plus-circle me-2"></i>
                Tambah Sparepart Baru
            </h4>
            <p class="text-muted">Isi formulir berikut untuk menambahkan sparepart baru ke sistem</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('spareparts.store') }}" method="POST" id="sparepartForm">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="code">
                            <i class="fas fa-barcode"></i>
                            Kode Sparepart <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            class="form-control @error('code') is-invalid @enderror"
                            id="code"
                            name="code"
                            value="{{ old('code', $autoCode) }}"
                            placeholder="Contoh: SPR-001"
                            readonly>
                        <small class="text-muted">Kode akan digenerate otomatis</small>
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">
                            <i class="fas fa-microchip"></i>
                            Nama Sparepart <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Masukkan nama sparepart"
                            required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category_id">
                            <i class="fas fa-tag"></i>
                            Kategori <span class="text-danger">*</span>
                        </label>
                        <select class="form-control @error('category_id') is-invalid @enderror"
                            id="category_id"
                            name="category_id"
                            required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="brand">
                            <i class="fas fa-building"></i>
                            Brand
                        </label>
                        <input type="text"
                            class="form-control @error('brand') is-invalid @enderror"
                            id="brand"
                            name="brand"
                            value="{{ old('brand') }}"
                            placeholder="Contoh: Viar Original, Karya, NGK">
                        @error('brand')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="purchase_price">
                            <i class="fas fa-money-bill-wave"></i>
                            Harga Beli <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                            class="form-control @error('purchase_price') is-invalid @enderror"
                            id="purchase_price"
                            name="purchase_price"
                            value="{{ old('purchase_price') }}"
                            placeholder="0"
                            required>
                        @error('purchase_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="selling_price">
                            <i class="fas fa-money-bill-wave"></i>
                            Harga Jual <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                            class="form-control @error('selling_price') is-invalid @enderror"
                            id="selling_price"
                            name="selling_price"
                            value="{{ old('selling_price') }}"
                            placeholder="0"
                            required>
                        @error('selling_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="stock">
                            <i class="fas fa-boxes"></i>
                            Stok Awal <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                            class="form-control @error('stock') is-invalid @enderror"
                            id="stock"
                            name="stock"
                            value="{{ old('stock', 0) }}"
                            placeholder="0"
                            required>
                        @error('stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="min_stock">
                            <i class="fas fa-exclamation-triangle"></i>
                            Stok Minimum <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                            class="form-control @error('min_stock') is-invalid @enderror"
                            id="min_stock"
                            name="min_stock"
                            value="{{ old('min_stock', 5) }}"
                            placeholder="5"
                            required>
                        <small class="text-muted">Peringatan akan muncul jika stok di bawah angka ini</small>
                        @error('min_stock')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="location_rack">
                    <i class="fas fa-map-marker-alt"></i>
                    Lokasi Rak
                </label>
                <input type="text"
                    class="form-control @error('location_rack') is-invalid @enderror"
                    id="location_rack"
                    name="location_rack"
                    value="{{ old('location_rack') }}"
                    placeholder="Contoh: A-01, B-03, C-02">
                @error('location_rack')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i>
                    Deskripsi
                </label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                    id="description"
                    name="description"
                    rows="3"
                    placeholder="Deskripsi sparepart (opsional)">{{ old('description') }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-save me-2"></i>
                    Simpan Sparepart
                </button>
                <a href="{{ route('spareparts.index') }}" class="btn btn-outline-gold">
                    <i class="fas fa-times me-2"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/sparepart-form.css') }}">
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('sparepartForm');

        if (form) {
            form.addEventListener('submit', function(e) {
                const name = document.getElementById('name').value;
                const purchasePrice = parseFloat(document.getElementById('purchase_price').value);
                const sellingPrice = parseFloat(document.getElementById('selling_price').value);

                if (!name) {
                    e.preventDefault();
                    showToast('Nama sparepart wajib diisi!', 'error');
                    return false;
                }

                if (sellingPrice < purchasePrice) {
                    e.preventDefault();
                    showToast('Harga jual tidak boleh lebih kecil dari harga beli!', 'error');
                    return false;
                }

                if (isNaN(purchasePrice) || purchasePrice <= 0) {
                    e.preventDefault();
                    showToast('Harga beli harus diisi dengan angka yang valid!', 'error');
                    return false;
                }

                if (isNaN(sellingPrice) || sellingPrice <= 0) {
                    e.preventDefault();
                    showToast('Harga jual harus diisi dengan angka yang valid!', 'error');
                    return false;
                }

                return true;
            });
        }

        function showToast(message, type = 'success') {
            const existingToasts = document.querySelectorAll('.toast-notification');
            existingToasts.forEach(toast => toast.remove());

            const toast = document.createElement('div');
            toast.className = `toast-notification toast-${type}`;
            toast.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
            ${message}
        `;
            document.body.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 100);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    });
</script>
@endpush