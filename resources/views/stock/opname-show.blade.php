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
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ url('/stock/opname/' . $stockOpname->id . '/print') }}" class="btn btn-outline-gold" target="_blank">
                <i class="fas fa-print me-2"></i> Cetak Berita Acara
            </a>
            <a href="{{ url('/stock/opname/' . $stockOpname->id . '/export') }}" class="btn btn-outline-gold">
                <i class="fas fa-file-excel me-2"></i> Export Excel
            </a>
            <a href="{{ route('stock.opname.index') }}" class="btn btn-outline-gold">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(212, 175, 55, 0.1); color: var(--gold);">
                    <i class="fas fa-barcode"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stockOpname->opname_number ?? '-' }}</h3>
                    <p>No. Opname</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(52, 152, 219, 0.1); color: #3498db;">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stockOpname->opname_date ? $stockOpname->opname_date->format('d/m/Y') : '-' }}</h3>
                    <p>Tanggal</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(46, 204, 113, 0.1); color: #2ecc71;">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stockOpname->period ?? '-' }}</h3>
                    <p>Periode</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon" style="background: rgba(243, 156, 18, 0.1); color: #f39c12;">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $stockOpname->creator->name ?? '-' }}</h3>
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
                    <th><i class="fas fa-hashtag"></i> No</th>
                    <th><i class="fas fa-barcode"></i> Kode</th>
                    <th><i class="fas fa-microchip"></i> Nama Sparepart</th>
                    <th><i class="fas fa-database"></i> Stok Sistem</th>
                    <th><i class="fas fa-clipboard-check"></i> Stok Fisik</th>
                    <th><i class="fas fa-chart-line"></i> Selisih</th>
                    <th><i class="fas fa-flag-checkered"></i> Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stockOpname->items ?? [] as $item)
                @php
                $differenceClass = ($item->difference ?? 0) >= 0 ? 'diff-up' : 'diff-down';
                $statusClass = ($item->difference ?? 0) == 0 ? 'status-completed' : (($item->difference ?? 0) > 0 ? 'stock-normal' : 'stock-critical');
                $statusText = ($item->difference ?? 0) == 0 ? 'Sesuai' : (($item->difference ?? 0) > 0 ? 'Kelebihan' : 'Kekurangan');
                @endphp
                <tr>
                    <td data-label="No">{{ $loop->iteration }}</td>
                    <td data-label="Kode">{{ $item->sparepart->code ?? '-' }}</td>
                    <td data-label="Nama Sparepart" style="text-align: left;">{{ $item->sparepart->name ?? '-' }}</td>
                    <td data-label="Stok Sistem">{{ number_format($item->system_stock ?? 0) }} pcs</td>
                    <td data-label="Stok Fisik">{{ number_format($item->physical_stock ?? 0) }} pcs</td>
                    <td data-label="Selisih">
                        <span class="{{ $differenceClass }}">
                            {{ ($item->difference ?? 0) >= 0 ? '+' : '' }}{{ number_format($item->difference ?? 0) }} pcs
                        </span>
                    </td>
                    <td data-label="Status">
                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="fas fa-database fa-2x mb-3 d-block" style="color: var(--text-muted);"></i>
                        <p class="text-muted">Tidak ada data item</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($stockOpname->notes ?? false)
    <div class="form-card mt-4">
        <div class="form-group">
            <label><i class="fas fa-sticky-note me-2" style="color: var(--gold);"></i> Catatan</label>
            <p class="text-muted">{{ $stockOpname->notes }}</p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
@endpush