@extends('layouts.master')

@section('title', 'Detail Stock Opname')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-clipboard-list me-2"></i>
                Detail Stock Opname
            </h4>
            <p class="text-muted">{{ $stockOpname->opname_number }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock-opname.export', $stockOpname->id) }}" class="btn btn-outline-gold" title="Download Excel">
                <i class="fas fa-file-excel me-2"></i> Export Excel
            </a>
            <a href="{{ route('stock-opname.print', $stockOpname->id) }}" class="btn btn-outline-gold" target="_blank" title="Cetak">
                <i class="fas fa-print me-2"></i> Cetak
            </a>
            <a href="{{ route('stock-opname.index') }}" class="btn btn-outline-gold">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $stockOpname->opname_number }}</h3>
                    <p>No. Opname</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $stockOpname->opname_date->format('d/m/Y') }}</h3>
                    <p>Tanggal</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $stockOpname->period }}</h3>
                    <p>Periode</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-info">
                    <h3>{{ $stockOpname->creator->name }}</h3>
                    <p>Dibuat oleh</p>
                </div>
            </div>
        </div>
    </div>

    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Stok Sistem</th>
                    <th>Stok Fisik</th>
                    <th>Selisih</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockOpname->items as $index => $item)
                <tr>
                    <td data-label="No">{{ $loop->iteration }}</a>
                    <td data-label="Kode">{{ $item->sparepart->code }}</a>
                    <td data-label="Nama Barang">{{ $item->sparepart->name }}</a>
                    <td data-label="Stok Sistem">{{ number_format($item->system_stock) }} pcs</a>
                    <td data-label="Stok Fisik">{{ number_format($item->physical_stock) }} pcs</a>
                    <td data-label="Selisih" class="{{ $item->difference >= 0 ? 'text-success' : 'text-danger' }}">
                        {{ $item->difference >= 0 ? '+' : '' }}{{ number_format($item->difference) }} pcs
                        </a>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($stockOpname->notes)
    <div class="form-card mt-4">
        <div class="form-group">
            <label><i class="fas fa-sticky-note me-2"></i> Catatan</label>
            <p class="text-muted">{{ $stockOpname->notes }}</p>
        </div>
    </div>
    @endif
</div>
@endsection