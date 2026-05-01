@extends('layouts.master')

@section('title', 'Detail Sparepart')

@section('content')
<div class="detail-sparepart-container">
    <!-- Header -->
    <div class="detail-header">
        <a href="{{ route('spareparts.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h4>
                <i class="fas fa-microchip me-2"></i>
                Detail Sparepart: {{ $sparepart->code }}
            </h4>
            <p class="text-muted">Informasi lengkap sparepart {{ $sparepart->name }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="detail-content">
        <div class="row">
            <!-- Informasi Sparepart -->
            <div class="col-md-6">
                <div class="info-card">
                    <div class="info-header">
                        <h5><i class="fas fa-info-circle me-2"></i>Informasi Produk</h5>
                    </div>
                    <div class="info-body">
                        <table class="info-table">
                            <tr>
                                <th><i class="fas fa-barcode"></i> Kode</th>
                                <td><span class="code-badge">{{ $sparepart->code }}</span></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-microchip"></i> Nama</th>
                                <td>{{ $sparepart->name }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-tag"></i> Kategori</th>
                                <td><span class="category-badge">{{ $sparepart->category->name }}</span></td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-building"></i> Brand</th>
                                <td>{{ $sparepart->brand ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-money-bill-wave"></i> Harga Beli</th>
                                <td class="price-text">Rp {{ number_format($sparepart->purchase_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-money-bill-wave"></i> Harga Jual</th>
                                <td class="price-text gold">Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-boxes"></i> Stok</th>
                                <td>
                                    @php
                                    $stockClass = '';
                                    $stockIcon = '';
                                    if ($sparepart->stock <= 0) {
                                        $stockClass='stock-critical' ;
                                        $stockIcon='fa-times-circle' ;
                                        $stockText='Habis' ;
                                        } elseif ($sparepart->isLowStock()) {
                                        $stockClass = 'stock-low';
                                        $stockIcon = 'fa-exclamation-triangle';
                                        $stockText = $sparepart->stock . ' pcs (Menipis)';
                                        } else {
                                        $stockClass = 'stock-normal';
                                        $stockIcon = 'fa-check-circle';
                                        $stockText = $sparepart->stock . ' pcs';
                                        }
                                        @endphp
                                        <span class="stock-badge {{ $stockClass }}">
                                            <i class="fas {{ $stockIcon }} me-1"></i>
                                            {{ $stockText }}
                                        </span>
                                        @if($sparepart->stock <= $sparepart->min_stock && $sparepart->stock > 0)
                                            <small class="text-warning d-block mt-1">Minimal stok: {{ $sparepart->min_stock }} pcs</small>
                                            @elseif($sparepart->stock <= 0)
                                                <small class="text-danger d-block mt-1">Segera restock!</small>
                                                @endif
                                </td>
                            </tr>
                            <tr>
                                <th><i class="fas fa-map-marker-alt"></i> Lokasi Rak</th>
                                <td>{{ $sparepart->location_rack ?? '-' }}</td>
                            </tr>
                            @if($sparepart->description)
                            <tr>
                                <th><i class="fas fa-align-left"></i> Deskripsi</th>
                                <td>{{ $sparepart->description }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            <!-- Barcode Section -->
            <div class="col-md-6">
                <div class="barcode-card">
                    <div class="barcode-header">
                        <h5><i class="fas fa-qrcode me-2"></i>Barcode CODE 128</h5>
                    </div>
                    <div class="barcode-body text-center">
                        <div class="barcode-wrapper">
                            {!! $barcodeHtml !!}
                        </div>
                        <div class="barcode-code">
                            <strong>{{ $sparepart->code }}</strong>
                        </div>
                        <div class="barcode-actions">
                            <a href="{{ route('barcode.download', $sparepart->code) }}"
                                class="btn btn-outline-gold"
                                target="_blank">
                                <i class="fas fa-download me-2"></i> Download Barcode
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/sparepart-detail.css') }}">
@endpush