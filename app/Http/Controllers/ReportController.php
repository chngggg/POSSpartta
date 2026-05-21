<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\StockCard;
use App\Models\StockOpname;
use App\Models\PurchaseReceipt;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentitem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
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

        // Total Penjualan (Nilai uang dari transaksi)
        $totalSales = Transaction::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('total_amount');

        // Barang Keluar (Jumlah item yang terjual) untuk periode yang dipilih
        $transactions = Transaction::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();

        $totalOutgoingQuantity = 0;
        foreach ($transactions as $transaction) {
            $items = $transaction->items;
            if (is_string($items)) {
                $items = json_decode($items, true);
            }
            if (is_array($items)) {
                foreach ($items as $item) {
                    $totalOutgoingQuantity += $item['quantity'] ?? 0;
                }
            }
        }

        // Data untuk grafik dual axis - PER BULAN
        $monthlySales = []; // Nominal penjualan per bulan (Rp)
        $monthlyOutgoingQuantities = []; // Barang keluar per bulan (pcs)

        for ($i = 1; $i <= 12; $i++) {
            // Nominal penjualan per bulan
            $monthlySales[$i] = Transaction::whereYear('created_at', $year)
                ->whereMonth('created_at', $i)
                ->sum('total_amount');

            // Barang keluar per bulan (jumlah item)
            $transactionsPerMonth = Transaction::whereYear('created_at', $year)
                ->whereMonth('created_at', $i)
                ->get();

            $outgoingQty = 0;
            foreach ($transactionsPerMonth as $transaction) {
                $items = $transaction->items;
                if (is_string($items)) {
                    $items = json_decode($items, true);
                }
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $outgoingQty += $item['quantity'] ?? 0;
                    }
                }
            }
            $monthlyOutgoingQuantities[$i] = $outgoingQty;
        }

        // Total Penyesuaian Stok
        $totalAdjustmentIn = DB::table('stock_adjustment_items')
            ->join('stock_adjustments', 'stock_adjustment_items.stock_adjustment_id', '=', 'stock_adjustments.id')
            ->where('stock_adjustments.type', 'in')
            ->whereYear('stock_adjustments.adjustment_date', $year)
            ->whereMonth('stock_adjustments.adjustment_date', $month)
            ->sum('stock_adjustment_items.quantity');

        $totalAdjustmentOut = DB::table('stock_adjustment_items')
            ->join('stock_adjustments', 'stock_adjustment_items.stock_adjustment_id', '=', 'stock_adjustments.id')
            ->where('stock_adjustments.type', 'out')
            ->whereYear('stock_adjustments.adjustment_date', $year)
            ->whereMonth('stock_adjustments.adjustment_date', $month)
            ->sum('stock_adjustment_items.quantity');

        // Top 10 sparepart stok terbanyak
        $topSpareparts = Sparepart::orderBy('stock', 'desc')->take(10)->get();

        // History Penjualan (10 terbaru)
        $recentTransactions = Transaction::orderBy('created_at', 'desc')->take(10)->get();

        return view('reports.index', compact(
            'period',
            'stockOpname',
            'totalSales',
            'totalOutgoingQuantity',
            'monthlySales',
            'monthlyOutgoingQuantities',
            'totalAdjustmentIn',
            'totalAdjustmentOut',
            'topSpareparts',
            'recentTransactions'
        ));
    }

    /**
     * Detail Transaksi / History Penjualan
     * Menampilkan nota dengan format yang sama seperti di POS
     */
    public function transactionDetail($id)
    {
        $transaction = Transaction::with('user')->findOrFail($id);

        // Ambil items (Laravel sudah otomatis decode JSON)
        $items = $transaction->items;

        // Jika masih string, decode
        if (is_string($items)) {
            $items = json_decode($items, true);
        }

        // Pastikan array
        $items = is_array($items) ? $items : [];

        // Ambil detail sparepart untuk setiap item
        foreach ($items as $key => $item) {
            if (isset($item['id'])) {
                $sparepart = Sparepart::find($item['id']);
                $items[$key]['code'] = $sparepart->code ?? '-';
                $items[$key]['name'] = $sparepart->name ?? '-';
                if (!isset($item['price']) || $item['price'] == 0) {
                    $items[$key]['price'] = $sparepart->selling_price ?? 0;
                } else {
                    $items[$key]['code'] = '-';
                    $items[$key]['name'] = '-';
                    $items[$key]['price'] = 0;
                }
                $items[$key]['quantity'] = $item['quantity'] ?? 1;
            } else {
                $items[$key]['code'] = '-';
                $items[$key]['name'] = '-';
                $items[$key]['price'] = 0;
                $items[$key]['quantity'] = 1;
            }
        }

        return view('reports.transaction-detail', compact('transaction', 'items'));
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
        $format = $request->get('format', 'excel');

        return redirect()->back()->with('info', 'Fitur export sedang dalam pengembangan.');
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
            $sparepart->total_purchased = PurchaseReceipt::whereHas('items', function ($q) use ($sparepart) {
                $q->where('sparepart_id', $sparepart->id);
            })
                ->whereBetween('receipt_date', [$startDate, $endDate])
                ->sum(DB::raw('(SELECT SUM(quantity) FROM purchase_receipt_items WHERE purchase_receipt_id = purchase_receipts.id AND sparepart_id = ' . $sparepart->id . ')'));

            $sparepart->total_sold = 0;
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
