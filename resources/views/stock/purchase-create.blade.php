@extends('layouts.master')

@section('title', 'Tambah Barang Masuk')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-plus-circle me-2"></i>
                Tambah Barang Masuk
            </h4>
            <p class="text-muted">Input bukti penerimaan barang / purchase receipt</p>
        </div>
        <a href="{{ route('stock.purchase.index') }}" class="btn btn-outline-gold">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <form id="purchaseForm" action="{{ route('stock.purchase.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="receipt_date">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Tanggal Penerimaan <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="receipt_date" name="receipt_date"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id">
                            <i class="fas fa-truck me-1"></i>
                            Supplier / Pemasok
                        </label>
                        <select class="form-control" id="supplier_id" name="supplier_id">
                            <option value="">Pilih Supplier (Opsional)</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="invoice_number">
                            <i class="fas fa-file-invoice me-1"></i>
                            Nomor Invoice
                        </label>
                        <input type="text" class="form-control" id="invoice_number" name="invoice_number"
                            placeholder="Nomor invoice dari supplier">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="notes">
                            <i class="fas fa-sticky-note me-1"></i>
                            Catatan
                        </label>
                        <textarea class="form-control" id="notes" name="notes" rows="1"
                            placeholder="Catatan tambahan (opsional)"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-microchip me-1"></i>
                    Daftar Sparepart yang Diterima
                </label>
                <div class="item-list" id="itemList">
                    <div class="item-row template" style="display: none;">
                        <select class="form-control sparepart-select" name="items[__INDEX__][sparepart_id]" required>
                            <option value="">Pilih Sparepart</option>
                            @foreach($spareparts as $sparepart)
                            <option value="{{ $sparepart->id }}">{{ $sparepart->code }} - {{ $sparepart->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" class="form-control quantity-input" name="items[__INDEX__][quantity]"
                            placeholder="Jumlah" min="1" required>
                        <input type="number" class="form-control price-input" name="items[__INDEX__][purchase_price]"
                            placeholder="Harga Beli" min="0" step="1000" required>
                        <button type="button" class="btn-icon remove-item" onclick="removeItem(this)">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-gold btn-sm mt-2" onclick="addItem()">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Item
                </button>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-save me-2"></i> Simpan Barang Masuk
                </button>
                <a href="{{ route('stock.purchase.index') }}" class="btn btn-outline-gold">
                    <i class="fas fa-times me-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
<style>
    .item-row {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        padding: 12px;
        background: #1a1a1a;
        border-radius: 12px;
    }

    .item-row .sparepart-select {
        flex: 2;
    }

    .item-row .quantity-input {
        width: 120px;
    }

    .item-row .price-input {
        width: 150px;
    }

    .item-row .btn-icon {
        width: 36px;
        height: 36px;
    }

    @media (max-width: 768px) {
        .item-row {
            flex-wrap: wrap;
        }

        .item-row .sparepart-select {
            width: 100%;
        }

        .item-row .quantity-input {
            width: calc(50% - 6px);
        }

        .item-row .price-input {
            width: calc(50% - 6px);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    let itemIndex = {
        {
            count(old('items', []))
        }
    };

    function addItem() {
        const template = document.querySelector('.item-row.template');
        const newRow = template.cloneNode(true);
        const html = newRow.outerHTML.replace(/__INDEX__/g, itemIndex);
        const div = document.createElement('div');
        div.innerHTML = html;
        const itemList = document.getElementById('itemList');
        itemList.appendChild(div.firstChild);
        itemIndex++;
    }

    function removeItem(btn) {
        btn.closest('.item-row').remove();
    }

    // Tambah item awal jika belum ada
    if (document.querySelectorAll('.item-row:not(.template)').length === 0) {
        addItem();
    }
</script>
@endpush