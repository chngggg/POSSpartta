@extends('layouts.master')

@section('title', 'Kasir / POS')

@section('content')
<div class="pos-container">
    <!-- LEFT PANEL - Products -->
    <div class="pos-products">
        <div class="products-header">
            <h5><i class="fas fa-barcode"></i> Scan Barcode</h5>
            <div class="barcode-scanner">
                <input type="text" id="barcodeInput" placeholder="Scan atau ketik barcode..." autofocus>
                <button onclick="addProductByBarcode(document.getElementById('barcodeInput').value)">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>

        <div class="products-header" style="border-top: none;">
            <div class="category-filter" id="categoryFilter">
                <button class="filter-chip active" onclick="filterByCategory('all', this)">
                    <i class="fas fa-th-large"></i> Semua
                </button>
                @foreach($categories as $category)
                <button class="filter-chip" onclick="filterByCategory('{{ $category->id }}', this)">
                    <i class="fas fa-tag"></i> {{ $category->name }}
                </button>
                @endforeach
            </div>

            <div class="search-wrapper" style="margin-top: 15px;">
                <i class="fas fa-search"></i>
                <input type="text" id="searchProducts" class="search-products" placeholder="Cari sparepart...">
            </div>
        </div>

        <div class="products-grid" id="productsGrid">
            @foreach($spareparts as $sparepart)
            <div class="product-card"
                data-id="{{ $sparepart->id }}"
                data-code="{{ $sparepart->code }}"
                data-name="{{ $sparepart->name }}"
                data-price="{{ $sparepart->selling_price }}"
                data-stock="{{ $sparepart->stock }}"
                data-category="{{ $sparepart->category_id }}"
                onclick="addToCart({
                     id: {{ $sparepart->id }},
                     code: '{{ $sparepart->code }}',
                     name: '{{ $sparepart->name }}',
                     selling_price: {{ $sparepart->selling_price }},
                     stock: {{ $sparepart->stock }}
                 })">
                <div class="product-code">{{ $sparepart->code }}</div>
                <div class="product-name">{{ $sparepart->name }}</div>
                <div class="product-price">Rp {{ number_format($sparepart->selling_price, 0, ',', '.') }}</div>
                <div class="product-stock">
                    <i class="fas fa-boxes"></i> Stok: {{ $sparepart->stock }} pcs
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- RIGHT PANEL - Cart -->
    <div class="pos-cart">
        <div class="cart-header">
            <h5><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h5>
        </div>

        <div class="cart-items" id="cartItems">
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Keranjang kosong</p>
                <small class="text-muted">Pilih sparepart dari daftar</small>
            </div>
        </div>

        <div class="cart-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal">Rp 0</span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span id="totalAmount">Rp 0</span>
            </div>
            <div class="summary-row">
                <span>Item</span>
                <span id="itemCount">0</span>
            </div>

            <div class="cart-actions">
                <button class="btn btn-gold btn-process" onclick="processTransaction()">
                    <i class="fas fa-check-circle"></i> Proses Pembayaran
                </button>
                <button class="btn btn-clear" onclick="clearCart()">
                    <i class="fas fa-trash-alt"></i> Kosongkan
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/pos.css') }}">
@endpush