<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\StockCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin,admin,']);
    }

    public function index()
    {
        $stockOpnames = StockOpname::with('creator')->orderBy('created_at', 'desc')->paginate(10);
        return view('stock.opname-index', compact('stockOpnames'));
    }

    public function create()
    {
        $spareparts = Sparepart::with('category')->get();
        return view('stock.opname-create', compact('spareparts'));
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

            $stockOpname = StockOpname::create([
                'opname_number' => $opnameNumber,
                'opname_date' => now(),
                'period' => $request->period,
                'created_by' => auth()->id(),
                'status' => 'completed',
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
                    'is_counted' => true
                ]);

                // Update actual stock if there's difference
                if ($difference != 0) {
                    $sparepart->update(['stock' => $physicalStock]);

                    // Record to stock card
                    StockCard::record(
                        $sparepart->id,
                        'adjustment',
                        abs($difference),
                        "Penyesuaian stok dari stock opname {$opnameNumber}",
                        $stockOpname->id
                    );
                }
            }

            DB::commit();

            return redirect()->route('stock.opname.index')
                ->with('success', 'Stock opname berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $stockOpname = StockOpname::with('items.sparepart', 'creator')->find($id);

        if (!$stockOpname) {
            return redirect()->route('stock.opname.index')
                ->with('error', 'Data stock opname tidak ditemukan!');
        }

        return view('stock.opname-show', compact('stockOpname'));
    }

    public function destroy($id)
    {
        try {
            $stockOpname = StockOpname::find($id);

            if (!$stockOpname) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data stock opname tidak ditemukan!'
                ], 404);
            }

            $stockOpname->delete();

            return response()->json([
                'success' => true,
                'message' => 'Stock opname berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export($id)
    {
        $stockOpname = StockOpname::with('items.sparepart')->findOrFail($id);

        // Prepare data for export
        $data = [];
        $data[] = ['No.', 'Kode', 'Nama Sparepart', 'Stok Sistem', 'Stok Fisik', 'Selisih', 'Status'];

        foreach ($stockOpname->items as $index => $item) {
            $status = $item->difference == 0 ? 'Sesuai' : ($item->difference > 0 ? 'Kelebihan' : 'Kekurangan');
            $data[] = [
                $index + 1,
                $item->sparepart->code,
                $item->sparepart->name,
                $item->system_stock . ' pcs',
                $item->physical_stock . ' pcs',
                ($item->difference >= 0 ? '+' : '') . $item->difference . ' pcs',
                $status
            ];
        }

        // Generate CSV
        $filename = 'stock-opname-' . $stockOpname->opname_number . '.csv';
        $handle = fopen('php://temp', 'w+');

        foreach ($data as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function printBeritaAcara($id)
    {
        $stockOpname = StockOpname::with('items.sparepart', 'creator')->findOrFail($id);
        return view('stock.opname-print', compact('stockOpname'));
    }
}
