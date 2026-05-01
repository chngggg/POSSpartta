@extends('layouts.master')

@section('title', 'Generate Barcode')

@section('content')
<div class="generate-barcode-container">
    <!-- Header -->
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-barcode me-2"></i>
                Generate Barcode
            </h4>
            <p class="text-muted">Pilih sparepart untuk generate barcode</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="search-wrapper">
            <i class="fas fa-search"></i>
            <input type="text" id="searchSparepart" class="search-input" placeholder="Cari sparepart...">
        </div>
        <div class="filter-actions">
            <button type="button" id="selectAllBtn" class="btn btn-outline-gold btn-sm">
                <i class="fas fa-check-double me-1"></i> Pilih Semua
            </button>
            <button type="button" id="deselectAllBtn" class="btn btn-outline-gold btn-sm">
                <i class="fas fa-times me-1"></i> Batal Pilih
            </button>
        </div>
    </div>

    <!-- Sparepart List -->
    <div class="sparepart-list-container">
        <form id="barcodeForm" action="{{ route('barcode.print-multiple') }}" method="POST" target="_blank">
            @csrf
            <div class="sparepart-grid" id="sparepartGrid">
                @foreach($spareparts as $sparepart)
                <div class="sparepart-card" data-name="{{ strtolower($sparepart->name) }}" data-code="{{ strtolower($sparepart->code) }}">
                    <div class="card-checkbox">
                        <input type="checkbox" name="codes[]" value="{{ $sparepart->code }}" id="sparepart_{{ $sparepart->id }}" class="sparepart-checkbox">
                        <label for="sparepart_{{ $sparepart->id }}"></label>
                    </div>
                    <div class="card-info">
                        <div class="card-code">{{ $sparepart->code }}</div>
                        <div class="card-name">{{ $sparepart->name }}</div>
                        <div class="card-category">
                            <i class="fas fa-tag"></i> {{ $sparepart->category->name }}
                        </div>
                    </div>
                    <div class="card-price">
                        Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>

            <div class="form-actions">
                <button type="submit" id="printSelectedBtn" class="btn btn-gold">
                    <i class="fas fa-print me-2"></i>
                    Cetak Barcode Terpilih
                </button>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-gold">
                    <i class="fas fa-times me-2"></i>
                    Batal
                </a>
            </div>
            <div id="selectedCount" class="selected-count">
                <i class="fas fa-check-circle"></i> <span>0</span> sparepart dipilih
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/barcode-generate.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/js/barcode-generate.js') }}"></script>
@endpush