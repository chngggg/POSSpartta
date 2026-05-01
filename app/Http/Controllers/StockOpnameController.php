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
        $this->middleware(['auth', 'role:super-admin,admin']);
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

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load('items.sparepart.category', 'creator');
        return view('stock.opname-show', compact('stockOpname'));
    }

    public function destroy(StockOpname $stockOpname)
    {
        $stockOpname->delete();
        return redirect()->route('stock.opname.index')
            ->with('success', 'Stock opname berhasil dihapus!');
    }

    public function export(StockOpname $stockOpname)
    {
        return redirect()->back()->with('info', 'Fitur export sedang dalam pengembangan');
    }

    public function printBeritaAcara($id)
    {
        $stockOpname = StockOpname::with('items.sparepart', 'creator')->findOrFail($id);
        return view('stock.opname-print', compact('stockOpname'));
    }
}
