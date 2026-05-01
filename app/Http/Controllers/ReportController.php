<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\StockCard;
use App\Models\StockOpname;
use App\Models\PurchaseReceipt;
use App\Models\StockAdjustment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin,admin']);
    }

    /**
     * Dashboard Laporan Utama
     */
    public function index(Request $request)
    {
        $period = $request->get('period', date('Y-m'));
        $year = substr($period, 0, 4);
        $month = substr($period, 5, 2);

        // Ringkasan Stock Opname
        $stockOpname = StockOpname::where('period', $period)->first();

        // Total Pembelian bulan ini
        $totalPurchase = PurchaseReceipt::whereYear('receipt_date', $year)
            ->whereMonth('receipt_date', $month)
            ->sum(DB::raw('(SELECT SUM(quantity * purchase_price) FROM purchase_receipt_items WHERE purchase_receipt_id = purchase_receipts.id)'));

        // Total Penjualan bulan ini
        $totalSales = Transaction::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('total_amount');

        // Total Penyesuaian Stok
        $totalAdjustmentIn = StockAdjustment::where('type', 'in')
            ->whereYear('adjustment_date', $year)
            ->whereMonth('adjustment_date', $month)
            ->count();

        $totalAdjustmentOut = StockAdjustment::where('type', 'out')
            ->whereYear('adjustment_date', $year)
            ->whereMonth('adjustment_date', $month)
            ->count();

        // Data untuk grafik
        $monthlySales = Transaction::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $monthlyPurchase = PurchaseReceipt::select(
            DB::raw('MONTH(receipt_date) as month'),
            DB::raw('SUM( (SELECT SUM(quantity * purchase_price) FROM purchase_receipt_items WHERE purchase_receipt_id = purchase_receipts.id) ) as total')
        )
            ->whereYear('receipt_date', $year)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Top 10 sparepart tersedia
        $topSpareparts = Sparepart::orderBy('stock', 'desc')->take(10)->get();

        // Top 10 sparepart yang sering dibeli
        $topPurchased = PurchaseReceipt::select('spareparts.id', 'spareparts.name', 'spareparts.code')
            ->join('purchase_receipt_items', 'purchase_receipts.id', '=', 'purchase_receipt_items.purchase_receipt_id')
            ->join('spareparts', 'purchase_receipt_items.sparepart_id', '=', 'spareparts.id')
            ->selectRaw('SUM(purchase_receipt_items.quantity) as total_quantity')
            ->groupBy('spareparts.id', 'spareparts.name', 'spareparts.code')
            ->orderBy('total_quantity', 'desc')
            ->take(10)
            ->get();

        return view('reports.index', compact(
            'period',
            'stockOpname',
            'totalPurchase',
            'totalSales',
            'totalAdjustmentIn',
            'totalAdjustmentOut',
            'monthlySales',
            'monthlyPurchase',
            'topSpareparts',
            'topPurchased'
        ));
    }

    /**
     * Laporan Kartu Stok (Stock Card Report)
     */
    public function stockCard(Request $request)
    {
        $sparepartId = $request->get('sparepart_id');
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));

        $spareparts = Sparepart::orderBy('name')->get();
        $selectedSparepart = null;
        $stockCards = collect();

        if ($sparepartId) {
            $selectedSparepart = Sparepart::find($sparepartId);
            $stockCards = StockCard::with('sparepart')
                ->where('sparepart_id', $sparepartId)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'asc')
                ->get();
        }

        return view('reports.stock-card', compact(
            'spareparts',
            'selectedSparepart',
            'stockCards',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Laporan Mutasi Barang
     */
    public function mutation(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));

        // Barang Masuk (Purchase)
        $incoming = PurchaseReceipt::with('items.sparepart')
            ->whereBetween('receipt_date', [$startDate, $endDate])
            ->get();

        // Barang Keluar (Sales from Transactions)
        $outgoing = Transaction::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        // Penyesuaian Stok
        $adjustments = StockAdjustment::with('items.sparepart')
            ->whereBetween('adjustment_date', [$startDate, $endDate])
            ->get();

        return view('reports.mutation', compact('incoming', 'outgoing', 'adjustments', 'startDate', 'endDate'));
    }

    /**
     * Laporan Stock Opname
     */
    public function stockOpname(Request $request)
    {
        $period = $request->get('period', date('Y-m'));

        $stockOpname = StockOpname::with('items.sparepart.category', 'creator')
            ->where('period', $period)
            ->first();

        if (!$stockOpname) {
            return redirect()->route('reports.index')->with('info', 'Belum ada data stock opname untuk periode ' . $period);
        }

        return view('reports.stock-opname', compact('stockOpname', 'period'));
    }

    /**
     * Export ke Excel (akan diimplementasikan dengan Maatwebsite Excel)
     */
    public function export(Request $request)
    {
        $type = $request->get('type');
        $format = $request->get('format', 'excel'); // excel or pdf

        // This will be implemented with Maatwebsite Excel package
        // Untuk sementara redirect dengan pesan info

        return redirect()->back()->with('info', 'Fitur export sedang dalam pengembangan. Install package: composer require maatwebsite/excel');
    }

    /**
     * Laporan Summary Per Sparepart
     */
    public function summary(Request $request)
    {
        $startDate = $request->get('start_date', date('Y-m-01'));
        $endDate = $request->get('end_date', date('Y-m-t'));

        $spareparts = Sparepart::with('category')->get();

        foreach ($spareparts as $sparepart) {
            // Total pembelian sparepart ini
            $sparepart->total_purchased = PurchaseReceipt::whereHas('items', function ($q) use ($sparepart) {
                $q->where('sparepart_id', $sparepart->id);
            })
                ->whereBetween('receipt_date', [$startDate, $endDate])
                ->sum(DB::raw('(SELECT SUM(quantity) FROM purchase_receipt_items WHERE purchase_receipt_id = purchase_receipts.id AND sparepart_id = ' . $sparepart->id . ')'));

            // Total penjualan sparepart ini (dari transaction items)
            // Note: Ini memerlukan tabel transaction_items jika ingin detail per sparepart
            $sparepart->total_sold = 0; // Implement if transaction_items table exists

            // Nilai stok (harga beli terakhir * stok)
            $sparepart->stock_value = $sparepart->stock * $sparepart->purchase_price;
        }

        return view('reports.summary', compact('spareparts', 'startDate', 'endDate'));
    }

    /**
     * Laporan Keuangan Sederhana
     */
    public function financial(Request $request)
    {
        $year = $request->get('year', date('Y'));

        // Pendapatan per bulan
        $revenues = Transaction::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total')
        )
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Pengeluaran per bulan (pembelian)
        $expenses = PurchaseReceipt::select(
            DB::raw('MONTH(receipt_date) as month'),
            DB::raw('SUM( (SELECT SUM(quantity * purchase_price) FROM purchase_receipt_items WHERE purchase_receipt_id = purchase_receipts.id) ) as total')
        )
            ->whereYear('receipt_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Profit/Loss per bulan
        $profits = [];
        for ($i = 1; $i <= 12; $i++) {
            $revenue = $revenues[$i] ?? 0;
            $expense = $expenses[$i] ?? 0;
            $profits[$i] = $revenue - $expense;
        }

        $totalRevenue = array_sum($revenues);
        $totalExpense = array_sum($expenses);
        $totalProfit = $totalRevenue - $totalExpense;

        return view('reports.financial', compact(
            'year',
            'revenues',
            'expenses',
            'profits',
            'totalRevenue',
            'totalExpense',
            'totalProfit'
        ));
    }
}
