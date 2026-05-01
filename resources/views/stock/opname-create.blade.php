@extends('layouts.master')

@section('title', 'Tambah Stock Opname')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-plus-circle me-2"></i>
                Stock Opname Baru
            </h4>
            <p class="text-muted">Lakukan perhitungan fisik barang (stock opname)</p>
        </div>
        <a href="{{ route('stock.opname.index') }}" class="btn btn-outline-gold">
            <i class="fas fa-arrow-left me-2"></i> Kembali
        </a>
    </div>

    <div class="form-card">
        <form id="stockOpnameForm" action="{{ route('stock.opname.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="period">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Periode Opname <span class="text-danger">*</span>
                        </label>
                        <input type="month" class="form-control" id="period" name="period"
                            value="{{ date('Y-m') }}" required>
                        <small class="text-muted">Pilih bulan dan tahun opname</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="notes">
                            <i class="fas fa-sticky-note me-1"></i>
                            Catatan
                        </label>
                        <textarea class="form-control" id="notes" name="notes" rows="1"
                            placeholder="Catatan tentang stock opname (opsional)"></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>
                    <i class="fas fa-microchip me-1"></i>
                    Daftar Sparepart
                </label>
                <div class="item-list">
                    @foreach($spareparts as $sparepart)
                    <div class="item-row">
                        <div class="item-code">{{ $sparepart->code }}</div>
                        <div class="item-name">{{ $sparepart->name }}</div>
                        <div class="item-stock">
                            <span class="badge {{ $sparepart->isLowStock() ? 'stock-low' : 'stock-normal' }}">
                                {{ $sparepart->stock }} pcs
                            </span>
                        </div>
                        <div class="item-input">
                            <input type="number"
                                name="items[{{ $loop->index }}][physical_stock]"
                                class="form-control physical-stock-input"
                                data-system-stock="{{ $sparepart->stock }}"
                                placeholder="Stok Fisik"
                                value="{{ $sparepart->stock }}">
                            <input type="hidden" name="items[{{ $loop->index }}][sparepart_id]" value="{{ $sparepart->id }}">
                        </div>
                        <div class="item-difference">
                            <span class="difference-value">0</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-gold">
                    <i class="fas fa-save me-2"></i> Simpan Stock Opname
                </button>
                <a href="{{ route('stock.opname.index') }}" class="btn btn-outline-gold">
                    <i class="fas fa-times me-2"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/js/stock.js') }}"></script>
@endpush