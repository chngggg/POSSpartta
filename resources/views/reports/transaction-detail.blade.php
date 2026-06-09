@extends('layouts.master')

@section('title', 'Detail Transaksi')

@section('content')
<div class="stock-container">
    <div class="page-header">
        <div>
            <h4>
                <i class="fas fa-receipt me-2"></i>
                Detail Transaksi
            </h4>
            <p class="text-muted">{{ $transaction->transaction_id }}</p>
        </div>
        <div>
            <button class="btn btn-gold no-print" onclick="printReceipt()">
                <i class="fas fa-print me-2"></i> Cetak Faktur
            </button>
            <a href="{{ route('reports.index') }}" class="btn btn-outline-gold no-print">
                <i class="fas fa-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- NOTA / FAKTUR -->
    <div id="printArea" style="
        background: white;
        color: #1a1a1a;
        padding: 30px;
        font-family: 'Courier New', 'Times New Roman', monospace;
        max-width: 800px;
        margin: 0 auto;
        border-radius: 0;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    ">
        <!-- Kop Faktur -->
        <div style="text-align: center; margin-bottom: 25px; border-bottom: 2px solid #1a1a1a; padding-bottom: 15px;">
            <div style="font-size: 24px; font-weight: 800; letter-spacing: 3px; color: #d4af37;">SPARTTA POS</div>
            <div style="font-size: 11px; color: #555; margin-top: 5px;">Jl. Sringin Raya Rt.1 Rw.4 No. 18, Terboyo Wetan, Genuk, Semarang 50112</div>
            <div style="font-size: 10px; color: #777;">Telp: (024) 1234-5678 | Email: info@sparttapos.com</div>
            <div style="font-size: 10px; color: #777;">www.sparttapos.com</div>
        </div>

        <!-- Judul FAKTUR -->
        <div style="text-align: center; margin: 20px 0;">
            <div style="font-size: 18px; font-weight: 800; letter-spacing: 3px;">FAKTUR PENJUALAN</div>
            <div style="font-size: 11px; color: #888;">INVOICE / RECEIPT</div>
        </div>

        <!-- Info Faktur 2 Kolom -->
        @php
        $invoiceNumber = 'INV/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $formattedDate = $transaction->created_at->locale('id')->isoFormat('D MMMM Y');
        $formattedTime = $transaction->created_at->format('H:i:s');
        $methodNames = ['cash' => 'Tunai', 'qris' => 'QRIS', 'transfer' => 'Transfer Bank'];
        @endphp

        <table style="width: 100%; font-size: 11px; margin-bottom: 20px; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; padding: 4px 0;">
                    <strong>No. Faktur</strong><br>
                    {{ $invoiceNumber }}
                </td>
                <td style="width: 50%; padding: 4px 0;">
                    <strong>Tanggal</strong><br>
                    {{ $formattedDate }}
                </td>
            </tr>
            <tr>
                <td style="padding: 4px 0;">
                    <strong>ID Transaksi</strong><br>
                    {{ $transaction->transaction_id }}
                </td>
                <td style="padding: 4px 0;">
                    <strong>Waktu</strong><br>
                    {{ $formattedTime }}
                </td>
            </tr>
            <tr>
                <td style="padding: 4px 0;">
                    <strong>Kasir</strong><br>
                    {{ $transaction->user->name }}
                </td>
                <td style="padding: 4px 0;">
                    <strong>Metode Bayar</strong><br>
                    {{ $methodNames[$transaction->payment_method] ?? ucfirst($transaction->payment_method) }}
                </td>
            </tr>
        </table>

        <!-- Garis Pemisah -->
        <div style="border-top: 1px solid #ddd; margin: 10px 0;"></div>

        <!-- Header Tabel Barang -->
        <table style="width: 100%; font-size: 11px; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                    <th style="padding: 8px 5px; text-align: center;">No</th>
                    <th style="padding: 8px 5px; text-align: left;">Nama Barang</th>
                    <th style="padding: 8px 5px; text-align: center;">Qty</th>
                    <th style="padding: 8px 5px; text-align: right;">Harga</th>
                    <th style="padding: 8px 5px; text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 10px 5px; text-align: center;">{{ $index + 1 }}</td>
                    <td style="padding: 10px 5px;">
                        <div style="font-weight: 600;">{{ $item['name'] ?? '-' }}</div>
                        <div style="font-size: 9px; color: #888;">{{ $item['code'] ?? '-' }}</div>
                    </td>
                    <td style="padding: 10px 5px; text-align: center;">{{ $item['quantity'] ?? 0 }}</td>
                    <td style="padding: 10px 5px; text-align: right;">{{ isset($item['price']) && $item['price'] > 0 ? 'Rp ' . number_format($item['price'], 0, ',', '.') : '-' }}</td>
                    <td style="padding: 10px 5px; text-align: right; font-weight: 600; color: #2ecc71;">
                        {{ isset($item['price']) && isset($item['quantity']) && $item['price'] > 0 ? 'Rp ' . number_format($item['price'] * $item['quantity'], 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 20px; text-align: center; color: #999;">
                        <i class="fas fa-box-open"></i> Tidak ada item dalam transaksi ini
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Garis Pemisah -->
        <div style="border-top: 1px solid #ddd; margin: 15px 0;"></div>

        <!-- Ringkasan Pembayaran -->
        <table style="width: 100%; font-size: 12px; margin: 10px 0;">
            <tr>
                <td style="width: 70%; text-align: right; padding: 5px;">Subtotal:</td>
                <td style="width: 30%; text-align: right; padding: 5px; font-weight: 600;">
                    {{ number_format($transaction->total_amount, 0, ',', '.') }}
                </td>
            </tr>
            @if($transaction->payment_amount > $transaction->total_amount)
            <tr>
                <td style="text-align: right; padding: 5px;">Tunai:</td>
                <td style="text-align: right; padding: 5px;">{{ number_format($transaction->payment_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="text-align: right; padding: 5px;">Kembalian:</td>
                <td style="text-align: right; padding: 5px; color: #2ecc71; font-weight: 700;">
                    {{ number_format($transaction->change_amount, 0, ',', '.') }}
                </td>
            </tr>
            @endif
            <tr style="border-top: 2px solid #d4af37;">
                <td style="text-align: right; padding: 10px 5px 5px;"><strong>TOTAL</strong></td>
                <td style="text-align: right; padding: 10px 5px 5px;">
                    <strong style="font-size: 16px; color: #d4af37;">
                        {{ number_format($transaction->total_amount, 0, ',', '.') }}
                    </strong>
                </td>
            </tr>
        </table>

        <!-- Terbilang -->
        <!-- <div style="background: #f9f9f9; padding: 10px; margin: 15px 0; border-left: 3px solid #d4af37;">
            <div style="font-size: 10px; color: #666;">Terbilang:</div>
            <div style="font-size: 11px; font-weight: 600;" id="terbilangText"></div>
        </div> -->

        <!-- Footer -->
        <div style="text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ddd;">
            <div style="font-size: 11px; font-weight: 500;">✨ Terima kasih atas kunjungan Anda! ✨</div>
            <div style="font-size: 10px; color: #888; margin-top: 8px;">Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan</div>
            <div style="font-size: 9px; color: #aaa; margin-top: 10px;">This is a computer generated document, no signature required.</div>

            <!-- Label DUPLICATE untuk history -->
            <div style="margin-top: 15px; padding-top: 10px; border-top: 1px dashed #ddd;">
                <div style="font-size: 10px; color: #e74c3c; font-weight: bold; text-transform: uppercase; letter-spacing: 2px;">
                    ⚠️ DUPLICATE / COPY - BUKAN NOTA ASLI ⚠️
                </div>
                <div style="font-size: 9px; color: #999; margin-top: 5px;">
                    Dicetak dari history transaksi pada {{ now()->format('d/m/Y H:i:s') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stock.css') }}">
<style>
    @media print {

        .page-header .btn,
        .btn-outline-gold,
        .btn,
        .stock-container .page-header div:last-child,
        .no-print {
            display: none !important;
        }

        body {
            background: white;
            padding: 0;
            margin: 0;
        }

        #printArea {
            box-shadow: none;
            padding: 20px;
            margin: 0;
            max-width: 100%;
        }

        .invoice-footer {
            margin-top: 20px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Fungsi untuk mengkonversi angka ke terbilang
    function numberToWords(amount) {
        const angka = Math.floor(amount);
        const bilangan = [
            "", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"
        ];

        if (angka < 12) return bilangan[angka];
        if (angka < 20) return bilangan[angka - 10] + " Belas";
        if (angka < 100) {
            const puluhan = Math.floor(angka / 10);
            const satuan = angka % 10;
            return bilangan[puluhan] + " Puluh" + (satuan > 0 ? " " + bilangan[satuan] : "");
        }
        if (angka < 200) return "Seratus" + (angka > 100 ? " " + numberToWords(angka - 100) : "");
        if (angka < 1000) {
            const ratusan = Math.floor(angka / 100);
            const sisa = angka % 100;
            return bilangan[ratusan] + " Ratus" + (sisa > 0 ? " " + numberToWords(sisa) : "");
        }
        if (angka < 2000) return "Seribu" + (angka > 1000 ? " " + numberToWords(angka - 1000) : "");
        if (angka < 1000000) {
            const ribuan = Math.floor(angka / 1000);
            const sisa = angka % 1000;
            return numberToWords(ribuan) + " Ribu" + (sisa > 0 ? " " + numberToWords(sisa) : "");
        }
        if (angka < 1000000000) {
            const jutaan = Math.floor(angka / 1000000);
            const sisa = angka % 1000000;
            return numberToWords(jutaan) + " Juta" + (sisa > 0 ? " " + numberToWords(sisa) : "");
        }
        return "Angka terlalu besar";
    }

    // Set terbilang
    const totalAmount = {
        {
            $transaction - > total_amount
        }
    };
    document.getElementById('terbilangText').innerText = numberToWords(totalAmount) + " Rupiah";

    // Fungsi untuk print
    function printReceipt() {
        const printContent = document.getElementById('printArea').cloneNode(true);
        const printWindow = window.open('', '_blank');

        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Faktur Penjualan - SPARTTA POS</title>
                <style>
                    * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                    }
                    body {
                        font-family: 'Courier New', 'Times New Roman', monospace;
                        background: white;
                        padding: 20px;
                    }
                    @media print {
                        body {
                            padding: 0;
                            margin: 0;
                        }
                        .no-print {
                            display: none;
                        }
                    }
                    .no-print {
                        text-align: center;
                        margin-top: 20px;
                    }
                    .no-print button {
                        padding: 10px 20px;
                        margin: 5px;
                        cursor: pointer;
                        border: none;
                        border-radius: 5px;
                        font-size: 14px;
                    }
                    .no-print button:first-child {
                        background: #d4af37;
                        color: #000;
                    }
                    .no-print button:last-child {
                        background: #333;
                        color: #fff;
                    }
                </style>
            </head>
            <body>
                ${printContent.outerHTML}
                <div class="no-print">
                    <button onclick="window.print()">🖨️ Cetak Faktur</button>
                    <button onclick="window.close()">❌ Tutup</button>
                </div>
            </body>
            </html>
        `);

        printWindow.document.close();
    }
</script>
@endpush