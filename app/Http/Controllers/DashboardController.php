<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\Category;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats data
        $totalSpareparts = Sparepart::count();
        $totalCategories = Category::count();
        $lowStockItems = Sparepart::whereColumn('stock', '<=', 'min_stock')->count();
        $totalUsers = User::count();

        // Low stock spareparts for display
        $lowStockSpareparts = Sparepart::whereColumn('stock', '<=', 'min_stock')
            ->with('category')
            ->take(5)
            ->get();

        // Sales data real dari database
        $today = date('Y-m-d');
        $salesToday = Transaction::whereDate('created_at', $today)->sum('total_amount');
        $transactionsCount = Transaction::whereDate('created_at', $today)->count();
        $totalTransactions = Transaction::count();

        // Data untuk grafik penjualan 7 hari terakhir
        $salesData = [];
        $labels = [];
        for ($i = 6; $i >= 1; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = $this->getDayName($date);
            $salesData[] = (int) Transaction::whereDate('created_at', $date)->sum('total_amount');
        }
        // Hari ini
        $labels[] = $this->getDayName($today);
        $salesData[] = (int) $salesToday;

        // Data distribusi kategori (jumlah sparepart per kategori)
        $categoryDistribution = Category::withCount('spareparts')->get();
        $categoryLabels = $categoryDistribution->pluck('name')->toArray();
        $categoryValues = $categoryDistribution->pluck('spareparts_count')->toArray();

        // Target data dari database (gunakan Setting model)
        $targetSales = (int) Setting::get('target_sales', 3000000);
        $monthlyTotal = Transaction::whereMonth('created_at', date('m'))->sum('total_amount');
        $targetPercentage = $targetSales > 0 ? min(($monthlyTotal / $targetSales) * 100, 100) : 0;

        return view('dashboard', compact(
            'totalSpareparts',
            'totalCategories',
            'lowStockItems',
            'totalUsers',
            'lowStockSpareparts',
            'salesToday',
            'transactionsCount',
            'totalTransactions',
            'targetSales',
            'monthlyTotal',
            'targetPercentage',
            'salesData',
            'labels',
            'categoryLabels',
            'categoryValues'
        ));
    }

    public function getStats()
    {
        $today = date('Y-m-d');

        // Data 7 hari terakhir untuk grafik
        $salesData = [];
        $labels = [];
        for ($i = 6; $i >= 1; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = $this->getDayName($date);
            $salesData[] = (int) Transaction::whereDate('created_at', $date)->sum('total_amount');
        }
        $labels[] = $this->getDayName($today);
        $salesData[] = (int) Transaction::whereDate('created_at', $today)->sum('total_amount');

        // Data distribusi kategori
        $categoryDistribution = Category::withCount('spareparts')->get();

        return response()->json([
            'total_sparepart' => Sparepart::count(),
            'low_stock_count' => Sparepart::whereColumn('stock', '<=', 'min_stock')->count(),
            'sales_today' => (int) Transaction::whereDate('created_at', $today)->sum('total_amount'),
            'transactions_today' => Transaction::whereDate('created_at', $today)->count(),
            'total_transactions' => Transaction::count(),
            'sales_data' => $salesData,
            'sales_labels' => $labels,
            'category_labels' => $categoryDistribution->pluck('name'),
            'category_values' => $categoryDistribution->pluck('spareparts_count'),
            'target_sales' => (int) Setting::get('target_sales', 3000000),
            'monthly_sales' => Transaction::whereMonth('created_at', date('m'))->sum('total_amount'),
        ]);
    }

    /**
     * Update target penjualan
     */
    public function updateTarget(Request $request)
    {
        $request->validate([
            'target_sales' => 'required|numeric|min:0'
        ]);

        Setting::set('target_sales', $request->target_sales, 'number');

        return response()->json([
            'success' => true,
            'message' => 'Target penjualan berhasil diupdate',
            'target_sales' => $request->target_sales
        ]);
    }

    private function getDayName($date)
    {
        $dayOfWeek = date('N', strtotime($date));
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        return $days[$dayOfWeek - 1];
    }
}
