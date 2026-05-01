@extends('layouts.master')

@section('title', 'Penyesuaian Stok')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-exchange-alt me-2"></i>
                Penyesuaian Stok
            </h4>
            <p class="text-muted">Tambah / kurangi stok barang karena berbagai alasan</p>
        </div>
        <a href="{{ route('stock.adjustment.index') }}" class="btn btn-outline-gold">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <form id="adjustmentForm" action="{{ route('stock.adjustment.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="adjustment_date">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Tanggal Penyesuaian <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="adjustment_date" name="adjustment_date"
                            value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="type">
                            <i class="fas fa-exchange-alt me-1"></i>
                            Jenis Penyesuaian <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="in">Penambahan Stok (+) </option>
                            <option value="out">Pengurangan Stok (-) </option>
                            <option value="correction">Koreksi Stok</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="reason">
                    <i class="fas fa-align-left me-1"></i>
                    Alasan Penyesuaian <span class="text-danger">*</span>
                </label>
                <textarea class="form-control" id="reason" name="reason" rows="2"
                    placeholder="Contoh: Barang rusak, barang hilang, barang tambahan, dll" required></textarea>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-microchip me-1"></i>
                    Daftar Sparepart yang Disesuaikan
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
                        <textarea class="form-control reason-input" name="items[__INDEX__][reason]"
                            rows="1" placeholder="Alasan khusus (opsional)"></textarea>
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
                    <i class="fas fa-save me-2"></i> Simpan Penyesuaian
                </button>
                <a href="{{ route('stock.adjustment.index') }}" class="btn btn-outline-gold">
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
        flex-wrap: wrap;
    }

    .item-row .sparepart-select {
        flex: 2;
        min-width: 200px;
    }

    .item-row .quantity-input {
        width: 120px;
    }

    .item-row .reason-input {
        flex: 2;
        min-width: 200px;
    }

    .item-row .btn-icon {
        width: 36px;
        height: 36px;
    }

    @media (max-width: 768px) {

        .item-row .sparepart-select,
        .item-row .reason-input {
            width: 100%;
        }

        .item-row .quantity-input {
            width: calc(100% - 50px);
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
        document.getElementById('itemList').appendChild(div.firstChild);
        itemIndex++;
    }

    function removeItem(btn) {
        btn.closest('.item-row').remove();
    }

    if (document.querySelectorAll('.item-row:not(.template)').length === 0) {
        addItem();
    }
</script>
@endpush