@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<!-- Welcome Section -->
<div class="dashboard-welcome">
    <h2>
        <i class="fas fa-chart-line me-2" style="color: var(--gold);"></i>
        Dashboard
    </h2>
    <p>
        Selamat datang, <strong style="color: var(--gold);">{{ auth()->user()->name }}</strong>!
        Berikut ringkasan inventory hari ini.
    </p>
</div>

<!-- Stats Cards -->
<div class="dashboard-cards">
    <div class="card-stats">
        <div class="card-icon">
            <i class="fas fa-microchip"></i>
        </div>
        <h3 id="totalSparepart">{{ $totalSpareparts ?? 0 }}</h3>
        <p>Total Sparepart</p>
        <div class="card-footer">
            <i class="fas fa-check-circle trend-up"></i>
            <span>Semua sparepart terdata</span>
        </div>
    </div>

    <div class="card-stats">
        <div class="card-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3 id="lowStockCount" class="text-warning">{{ $lowStockItems ?? 0 }}</h3>
        <p>Barang Menipis</p>
        <div class="card-footer">
            <i class="fas fa-clock trend-down"></i>
            <span>Perlu restock segera</span>
        </div>
    </div>

    <div class="card-stats">
        <div class="card-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <h3 id="salesToday">Rp {{ number_format($salesToday ?? 0, 0, ',', '.') }}</h3>
        <p>Penjualan Hari Ini</p>
        <div class="card-footer">
            <i class="fas fa-chart-line trend-up"></i>
            <span>{{ $transactionsCount ?? 0 }} transaksi</span>
        </div>
    </div>

    <div class="card-stats">
        <div class="card-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h3 id="totalTransactions">{{ $totalTransactions ?? 0 }}</h3>
        <p>Total Transaksi</p>
        <div class="card-footer">
            <i class="fas fa-calendar"></i>
            <span>Keseluruhan</span>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div class="row">
    <!-- Left Column -->
    <div class="col-lg-4">
        <!-- Target Penjualan Card -->
        <div class="chart-container mb-4" style="cursor: pointer;" onclick="openTargetModal()">
            <h6>
                <i class="fas fa-bullseye me-2"></i>
                Target Bulanan
                <i class="fas fa-pen text-muted ms-1" style="font-size: 0.7rem;"></i>
            </h6>
            <div class="target-card">
                <h2 id="targetSalesDisplay">Rp {{ number_format($targetSales ?? 3000000, 0, ',', '.') }}</h2>
                <div class="target-label">Target: Rp {{ number_format($targetSales ?? 3000000, 0, ',', '.') }}</div>
                <div class="progress mb-3">
                    <div class="progress-bar" role="progressbar"
                        style="width: {{ $targetPercentage ?? 0 }}%"
                        aria-valuenow="{{ $targetPercentage ?? 0 }}"
                        aria-valuemin="0" aria-valuemax="100">
                    </div>
                </div>
                <p class="mb-2">
                    <strong>{{ number_format($targetPercentage ?? 0, 1) }}%</strong> tercapai
                </p>
                <hr class="my-3" style="border-color: var(--border-light);">
                <div>
                    <small class="text-muted">Total bulan ini: </small>
                    <strong style="color: var(--gold);">
                        Rp {{ number_format($monthlyTotal ?? 0, 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>

        <!-- Generate Barcode Card -->
        <div class="chart-container mb-4">
            <h6>
                <i class="fas fa-barcode me-2"></i>
                Generate Barcode
            </h6>
            <p class="text-muted small mb-3">Buat barcode baru untuk sparepart</p>
            <div class="d-grid">
                <a href="{{ route('barcode.generate') }}" class="btn btn-gold">
                    <i class="fas fa-plus-circle me-2"></i>Generate Barcode
                </a>
            </div>
        </div>

        <!-- Scan Barcode Card -->
        <div class="chart-container mb-4">
            <h6>
                <i class="fas fa-qrcode me-2"></i>
                Scan Barcode
            </h6>
            <p class="text-muted small mb-3">Stock opname via barcode scanner</p>
            <div class="d-grid">
                <a href="{{ route('stock.opname.index') }}" class="btn btn-outline-gold">
                    <i class="fas fa-camera me-2"></i>Scan Barcode
                </a>
            </div>
            <small class="text-muted d-block mt-3 text-center">
                <i class="fas fa-boxes me-1"></i>Stock Opname
            </small>
        </div>
    </div>


    <!-- Right Column -->
    <div class="col-lg-8">
        <!-- Sales Chart -->
        <div class="chart-container mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h6 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Grafik Penjualan 7 Hari Terakhir
                </h6>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-gold active" data-period="week">Minggu</button>
                    <button type="button" class="btn btn-outline-gold" data-period="month">Bulan</button>
                    <button type="button" class="btn btn-outline-gold" data-period="year">Tahun</button>
                </div>
            </div>
            <canvas id="salesChart" height="280"></canvas>
        </div>

        <!-- Bottom Row -->
        <div class="row">
            <div class="col-md-6">
                <div class="chart-container">
                    <h6 class="mb-3">
                        <i class="fas fa-chart-pie me-2"></i>
                        Distribusi Kategori
                    </h6>
                    <canvas id="categoryChart" height="220"></canvas>
                </div>
            </div>

            <div class="col-md-6">
                <div class="chart-container">
                    <h6 class="mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Barang Menipis
                    </h6>
                    <div id="lowStockList" style="max-height: 260px; overflow-y: auto;">
                        @if(isset($lowStockSpareparts) && count($lowStockSpareparts) > 0)
                        @foreach($lowStockSpareparts as $item)
                        <div class="low-stock-item">
                            <h6>
                                <i class="fas fa-microchip me-1" style="font-size: 0.7rem;"></i>
                                {{ $item->name }}
                            </h6>
                            <div class="stock-info">
                                <span>
                                    <i class="fas fa-boxes me-1"></i>{{ $item->stock }} pcs
                                </span>
                                <span>
                                    <i class="fas fa-barcode me-1"></i>{{ $item->code }}
                                </span>
                            </div>
                            <small class="text-danger">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Min: {{ $item->min_stock }} pcs
                            </small>
                        </div>
                        @endforeach
                        @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3 d-block"></i>
                            <p class="text-muted mb-0">Semua stok aman</p>
                            <small class="text-muted">Tidak ada barang yang menipis</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Modal Edit Target Penjualan -->
<div class="modal fade" id="targetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-bullseye me-2"></i>
                    Target Penjualan Bulanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <label class="form-label">
                    <i class="fas fa-money-bill-wave me-1"></i>
                    Target Penjualan (Rp)
                </label>

                <div class="input-group custom-input">
                    <span class="input-group-text">Rp</span>
                    <input type="number"
                        class="form-control"
                        id="targetSalesInput"
                        value="{{ $targetSales ?? 3000000 }}"
                        step="100000">
                </div>

                <small class="text-muted">
                    Target penjualan untuk bulan ini
                </small>

                <!-- Info -->
                <div class="info-box mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Target saat ini:</span>
                        <strong class="text-gold">
                            Rp {{ number_format($targetSales ?? 3000000, 0, ',', '.') }}
                        </strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Pencapaian bulan ini:</span>
                        <strong class="text-success">
                            Rp {{ number_format($monthlyTotal ?? 0, 0, ',', '.') }}
                        </strong>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" class="btn btn-gold" onclick="updateTarget()">
                    <i class="fas fa-save me-2"></i> Simpan
                </button>
            </div>

        </div>
    </div>
</div>

<!-- CSS KHUSUS MODAL -->
<style>
    /* Modal utama */
    .modal-dark {
        background: linear-gradient(145deg, #0b0b0b, #121212);
        border-radius: 16px;
        border: 1px solid rgba(255, 215, 0, 0.15);
        color: #fff;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.7);
    }

    /* Header */
    .modal-dark .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .modal-dark .modal-title {
        color: var(--gold);
        font-weight: 600;
    }

    /* Body */
    .modal-dark .modal-body {
        color: #ddd;
    }

    /* Input */
    .custom-input .input-group-text {
        background: #111;
        border: 1px solid #222;
        color: var(--gold);
    }

    .custom-input .form-control {
        background: #0a0a0a;
        border: 1px solid #222;
        color: #fff;
    }

    .custom-input .form-control:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 0.15rem rgba(255, 215, 0, 0.2);
    }

    /* Info box */
    .info-box {
        background: #0a0a0a;
        border-radius: 12px;
        padding: 14px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* Gold text */
    .text-gold {
        color: var(--gold);
    }

    /* Buttons */
    .btn-gold {
        background: var(--gold);
        border: none;
        color: #000;
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-gold:hover {
        background: #e6c200;
        transform: translateY(-1px);
    }

    .btn-outline-gold {
        border: 1px solid var(--gold);
        color: var(--gold);
    }

    .btn-outline-gold:hover {
        background: var(--gold);
        color: #000;
    }

    /* Footer */
    .modal-dark .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* Backdrop biar elegan */
    .modal-backdrop.show {
        opacity: 0.85;
        backdrop-filter: blur(4px);
    }
</style>