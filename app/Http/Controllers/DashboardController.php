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

        // Data untuk grafik penjualan 7 hari terakhir (default Minggu)
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

    public function getStats(Request $request)
    {
        $period = $request->get('period', 'week');
        $year = date('Y');
        $month = date('m');

        $periodTitle = '';

        if ($period === 'week') {
            // Data 7 hari terakhir
            $salesData = [];
            $labels = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = date('Y-m-d', strtotime("-$i days"));
                $labels[] = $this->getDayName($date);
                $salesData[] = (int) Transaction::whereDate('created_at', $date)->sum('total_amount');
            }
            $periodTitle = 'Grafik Penjualan (7 Hari Terakhir)';
        } elseif ($period === 'month') {
            // Data per minggu dalam bulan ini - PERBAIKAN
            $labels = [];
            $salesData = [];

            // Hitung jumlah minggu dalam bulan ini
            $firstDay = date('Y-m-01');
            $lastDay = date('Y-m-t');
            $weeks = [];

            $start = strtotime($firstDay);
            $end = strtotime($lastDay);

            $weekNumber = 1;
            $currentWeekStart = $start;

            while ($currentWeekStart <= $end) {
                $weekEnd = min(strtotime('+6 days', $currentWeekStart), $end);
                $labels[] = "Minggu $weekNumber";

                $startDate = date('Y-m-d', $currentWeekStart);
                $endDate = date('Y-m-d', $weekEnd);

                $total = (int) Transaction::whereBetween('created_at', [
                    $startDate . ' 00:00:00',
                    $endDate . ' 23:59:59'
                ])->sum('total_amount');

                $salesData[] = $total;

                $weekNumber++;
                $currentWeekStart = strtotime('+7 days', $currentWeekStart);
            }

            // Jika kurang dari 4 minggu, tambahkan placeholder
            while (count($labels) < 4) {
                $labels[] = "Minggu " . (count($labels) + 1);
                $salesData[] = 0;
            }

            $periodTitle = 'Grafik Penjualan (Per Minggu - Bulan ' . date('F Y') . ')';
        } else { // year
            // Data per bulan dalam tahun ini
            $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
            $salesData = [];
            for ($i = 1; $i <= 12; $i++) {
                $salesData[] = (int) Transaction::whereYear('created_at', $year)
                    ->whereMonth('created_at', $i)
                    ->sum('total_amount');
            }
            $periodTitle = 'Grafik Penjualan (Per Bulan - Tahun ' . $year . ')';
        }

        // Data distribusi kategori
        $categoryDistribution = Category::withCount('spareparts')->get();

        return response()->json([
            'success' => true,
            'sales_data' => $salesData,
            'sales_labels' => $labels,
            'period_title' => $periodTitle,
            'category_labels' => $categoryDistribution->pluck('name'),
            'category_values' => $categoryDistribution->pluck('spareparts_count'),
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
