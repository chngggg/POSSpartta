@extends('layouts.master')

@section('title', 'Edit Sparepart')

@section('content')
<div class="sparepart-form-container">
    <!-- Header -->
    <div class="form-header">
        <a href="{{ route('spareparts.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4>
                <i class="fas fa-edit me-2"></i>
                Edit Sparepart
            </h4>
            <p class="text-muted">Edit informasi sparepart {{ $sparepart->name }}</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="form-card">
        <form action="{{ route('spareparts.update', $sparepart) }}" method="POST" id="sparepartForm">
            @csrf
            @method('PUT')

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
                            value="{{ old('code', $sparepart->code) }}"
                            placeholder="Contoh: SPR-001"
                            required>
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
                            value="{{ old('name', $sparepart->name) }}"
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
                            <option value="{{ $category->id }}" {{ old('category_id', $sparepart->category_id) == $category->id ? 'selected' : '' }}>
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
                            value="{{ old('brand', $sparepart->brand) }}"
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
                            value="{{ old('purchase_price', $sparepart->purchase_price) }}"
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
                            value="{{ old('selling_price', $sparepart->selling_price) }}"
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
                            Stok <span class="text-danger">*</span>
                        </label>
                        <input type="number"
                            class="form-control @error('stock') is-invalid @enderror"
                            id="stock"
                            name="stock"
                            value="{{ old('stock', $sparepart->stock) }}"
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
                            value="{{ old('min_stock', $sparepart->min_stock) }}"
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
                    value="{{ old('location_rack', $sparepart->location_rack) }}"
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
                    placeholder="Deskripsi sparepart (opsional)">{{ old('description', $sparepart->description) }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-save me-2"></i>
                    Update Sparepart
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
<style>
    /* Sparepart Form Container */
    .sparepart-form-container {
        animation: fadeInUp 0.5s ease;
    }

    /* Form Header */
    .form-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
    }

    .btn-back {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: #141414;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold);
        text-decoration: none;
        border: 1px solid var(--border-light);
        transition: all 0.3s ease;
    }

    .btn-back:hover {
        background: var(--gold);
        color: #0a0a0a;
        transform: translateX(-3px);
    }

    .form-header h4 {
        color: var(--gold);
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 4px 0;
    }

    .form-header p {
        color: var(--text-secondary);
        font-size: 0.85rem;
        margin: 0;
    }

    /* Form Card */
    .form-card {
        background: #141414;
        border-radius: 20px;
        border: 1px solid var(--border-light);
        padding: 30px;
    }

    /* Form Group */
    .form-group {
        margin-bottom: 24px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        color: var(--text-secondary);
        font-size: 0.85rem;
        font-weight: 500;
    }

    .form-group label i {
        color: var(--gold);
        margin-right: 8px;
        width: 18px;
    }

    /* Form Control */
    .form-control {
        background: #1a1a1a;
        border: 1px solid var(--border-light);
        border-radius: 12px;
        padding: 12px 16px;
        color: var(--text-primary);
        font-size: 0.85rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control:focus {
        background: #1a1a1a;
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        color: var(--text-primary);
        outline: none;
    }

    .form-control::placeholder {
        color: var(--text-muted);
    }

    /* Select */
    select.form-control {
        cursor: pointer;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23d4af37' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 16px center;
    }

    /* Textarea */
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* Invalid Feedback */
    .invalid-feedback {
        color: var(--danger);
        font-size: 0.7rem;
        margin-top: 5px;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-light);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .form-card {
            padding: 20px;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 768px) {
        .row {
            flex-direction: column;
        }

        .col-md-6 {
            width: 100%;
            padding: 0;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('sparepartForm');

        if (form) {
            form.addEventListener('submit', function(e) {
                // Validasi tambahan sebelum submit
                const code = document.getElementById('code').value;
                const name = document.getElementById('name').value;
                const purchasePrice = parseFloat(document.getElementById('purchase_price').value);
                const sellingPrice = parseFloat(document.getElementById('selling_price').value);

                if (!code || !name) {
                    e.preventDefault();
                    showToast('Kode dan Nama sparepart wajib diisi!', 'error');
                    return false;
                }

                if (sellingPrice < purchasePrice) {
                    e.preventDefault();
                    showToast('Harga jual tidak boleh lebih kecil dari harga beli!', 'error');
                    return false;
                }

                return true;
            });
        }

        function showToast(message, type = 'success') {
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