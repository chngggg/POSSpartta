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
            <p class="text-muted">Hasil perhitungan fisik barang</p>
        </div>
        <div>
            <a href="{{ route('stock.opname.print', $stockOpname->id) }}" class="btn btn-outline-gold" target="_blank">
                <i class="fas fa-print me-2"></i> Cetak Berita Acara
            </a>
            <a href="{{ route('stock.opname.export', $stockOpname->id) }}" class="btn btn-outline-gold">
                <i class="fas fa-file-export me-2"></i> Export Excel
            </a>
            <a href="{{ route('stock.opname.index') }}" class="btn btn-outline-gold">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Info Card -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(212, 175, 55, 0.1);">
                    <i class="fas fa-barcode"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stockOpname->opname_number }}</h3>
                    <p>No. Opname</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(52, 152, 219, 0.1);">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stockOpname->opname_date->format('d/m/Y') }}</h3>
                    <p>Tanggal</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(46, 204, 113, 0.1);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stockOpname->period }}</h3>
                    <p>Periode</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(243, 156, 18, 0.1);">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stockOpname->creator->name }}</h3>
                    <p>Dibuat oleh</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel hasil opname -->
    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Sparepart</th>
                    <th>Stok Sistem</th>
                    <th>Stok Fisik</th>
                    <th>Selisih</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockOpname->items as $index => $item)
                @php
                $differenceClass = $item->difference >= 0 ? 'diff-up' : 'diff-down';
                $statusClass = $item->difference == 0 ? 'status-completed' : ($item->difference > 0 ? 'stock-normal' : 'stock-critical');
                $statusText = $item->difference == 0 ? 'Sesuai' : ($item->difference > 0 ? 'Kelebihan' : 'Kekurangan');
                @endphp
                <tr>
                    <td data-label="No">{{ $loop->iteration }}</td>
                    <td data-label="Kode">{{ $item->sparepart->code }}</td>
                    <td data-label="Nama Sparepart">{{ $item->sparepart->name }}</td>
                    <td data-label="Stok Sistem">{{ number_format($item->system_stock) }} pcs</td>
                    <td data-label="Stok Fisik">{{ number_format($item->physical_stock) }} pcs</td>
                    <td data-label="Selisih">
                        <span class="{{ $differenceClass }}">
                            {{ $item->difference >= 0 ? '+' : '' }}{{ number_format($item->difference) }} pcs
                        </span>
                    </td>
                    <td data-label="Status">
                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($stockOpname->notes)
    <div class="form-card mt-4">
        <div class="form-group">
            <label><i class="fas fa-sticky-note"></i> Catatan</label>
            <p class="text-muted">{{ $stockOpname->notes }}</p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
@endpush