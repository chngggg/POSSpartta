<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $stockOpnames = StockOpname::with('creator')->orderBy('created_at', 'desc')->paginate(10);
        return view('stock-opname.index', compact('stockOpnames'));
    }

    public function create()
    {
        $spareparts = Sparepart::orderBy('name')->get();
        return view('stock-opname.create', compact('spareparts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'period' => 'required|string',
            'items' => 'required|array',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.physical_stock' => 'required|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            $opnameNumber = StockOpname::generateNumber();
            $status = $request->input('status', 'draft');

            $stockOpname = StockOpname::create([
                'opname_number' => $opnameNumber,
                'opname_date' => now(),
                'period' => $request->period,
                'created_by' => auth()->id(),
                'status' => $status,
                'notes' => $request->notes
            ]);

            foreach ($request->items as $item) {
                $sparepart = Sparepart::find($item['sparepart_id']);
                $systemStock = $sparepart->stock;
                $physicalStock = $item['physical_stock'];
                $difference = $physicalStock - $systemStock;

                StockOpnameItem::create([
                    'stock_opname_id' => $stockOpname->id,
                    'sparepart_id' => $item['sparepart_id'],
                    'system_stock' => $systemStock,
                    'physical_stock' => $physicalStock,
                    'difference' => $difference,
                    'notes' => $item['notes'] ?? null,
                ]);

                // Update stok jika finalisasi
                if ($status === 'completed') {
                    $sparepart->update(['stock' => $physicalStock]);
                }
            }

            DB::commit();

            $message = $status === 'draft'
                ? 'Stock opname disimpan sebagai Draft!'
                : 'Stock opname selesai! Stok telah diperbarui.';

            return redirect()->route('stock-opname.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $stockOpname = StockOpname::with('items.sparepart', 'creator')->findOrFail($id);
        return view('stock-opname.show', compact('stockOpname'));
    }

    public function destroy($id)
    {
        try {
            $stockOpname = StockOpname::findOrFail($id);
            $stockOpname->delete();

            return redirect()->route('stock-opname.index')
                ->with('success', 'Stock opname berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function print($id)
    {
        $stockOpname = StockOpname::with('items.sparepart', 'creator')->findOrFail($id);
        return view('stock-opname.print', compact('stockOpname'));
    }

    /**
     * Export stock opname to Excel/CSV
     */
    public function exportExcel($id)
    {
        $stockOpname = StockOpname::with('items.sparepart')->findOrFail($id);

        // Prepare data for CSV
        $filename = 'stock-opname-' . $stockOpname->opname_number . '.csv';
        $handle = fopen('php://temp', 'w+');

        // Add headers (UTF-8 BOM for Excel compatibility)
        fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Header row
        fputcsv($handle, [
            'No',
            'Kode',
            'Nama Barang',
            'Stok Sistem (pcs)',
            'Stok Fisik (pcs)',
            'Selisih (pcs)',
            'Status'
        ]);

        // Data rows
        foreach ($stockOpname->items as $index => $item) {
            $status = $item->difference == 0 ? 'Sesuai' : ($item->difference > 0 ? 'Kelebihan' : 'Kekurangan');
            fputcsv($handle, [
                $index + 1,
                $item->sparepart->code,
                $item->sparepart->name,
                $item->system_stock,
                $item->physical_stock,
                ($item->difference >= 0 ? '+' : '') . $item->difference,
                $status
            ]);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
