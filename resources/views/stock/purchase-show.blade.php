@extends('layouts.master')

@section('title', 'Detail Barang Masuk')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-download me-2"></i>
                Detail Barang Masuk
            </h4>
            <p class="text-muted">{{ $purchaseReceipt->receipt_number }}</p>
        </div>
        <div>
            <a href="{{ route('stock.purchase.index') }}" class="btn btn-outline-gold">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-card">
                <div class="form-group">
                    <label><i class="fas fa-calendar-alt"></i> Tanggal Penerimaan</label>
                    <p>{{ $purchaseReceipt->receipt_date->format('d F Y') }}</p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-truck"></i> Supplier</label>
                    <p>{{ $purchaseReceipt->supplier->name ?? '-' }}</p>
                </div>
                @if($purchaseReceipt->supplier && $purchaseReceipt->supplier->phone)
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Telepon Supplier</label>
                    <p>{{ $purchaseReceipt->supplier->phone }}</p>
                </div>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-card">
                <div class="form-group">
                    <label><i class="fas fa-file-invoice"></i> Nomor Invoice</label>
                    <p>{{ $purchaseReceipt->invoice_number ?? '-' }}</p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Dibuat oleh</label>
                    <p>{{ $purchaseReceipt->creator->name }}</p>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-clock"></i> Waktu Input</label>
                    <p>{{ $purchaseReceipt->created_at->format('d F Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($purchaseReceipt->notes)
    <div class="form-card mb-4">
        <div class="form-group">
            <label><i class="fas fa-sticky-note"></i> Catatan</label>
            <p>{{ $purchaseReceipt->notes }}</p>
        </div>
    </div>
    @endif

    <div class="table-premium-container">
        <table class="table-premium">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Sparepart</th>
                    <th>Jumlah</th>
                    <th>Harga Beli</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach($purchaseReceipt->items as $index => $item)
                @php $subtotal = $item->quantity * $item->purchase_price; $total += $subtotal; @endphp
                <tr>
                    <td data-label="No">{{ $loop->iteration }}</td>
                    <td data-label="Kode">{{ $item->sparepart->code }}</td>
                    <td data-label="Nama Sparepart">{{ $item->sparepart->name }}</td>
                    <td data-label="Jumlah">{{ number_format($item->quantity) }} pcs</td>
                    <td data-label="Harga Beli">{{ number_format($item->purchase_price, 0, ',', '.') }}</td>
                    <td data-label="Subtotal">{{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="border-top: 2px solid var(--border-gold);">
                    <td colspan="5" style="text-align: right;"><strong>TOTAL</strong></td>
                    <td><strong>{{ number_format($total, 0, ',', '.') }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($purchaseReceipt->attachment_path)
    <div class="form-card mt-4">
        <div class="form-group">
            <label><i class="fas fa-paperclip"></i> Lampiran</label>
            <p>
                <a href="{{ Storage::url($purchaseReceipt->attachment_path) }}" target="_blank" class="btn btn-outline-gold btn-sm">
                    <i class="fas fa-file-download"></i> Lihat Lampiran
                </a>
            </p>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
@endpush