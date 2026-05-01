<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:super-admin,admin']);
    }

    public function index()
    {
        $adjustments = StockAdjustment::with('creator', 'approver')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('stock.adjustment-index', compact('adjustments'));
    }

    public function create()
    {
        $spareparts = Sparepart::all();
        return view('stock.adjustment-create', compact('spareparts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'adjustment_date' => 'required|date',
            'type' => 'required|in:in,out,correction',
            'reason' => 'required|string',
            'items' => 'required|array',
            'items.*.sparepart_id' => 'required|exists:spareparts,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $adjustmentNumber = StockAdjustment::generateNumber();

            $adjustment = StockAdjustment::create([
                'adjustment_number' => $adjustmentNumber,
                'adjustment_date' => $request->adjustment_date,
                'type' => $request->type,
                'reason' => $request->reason,
                'created_by' => auth()->id(),
                'status' => 'approved'
            ]);

            foreach ($request->items as $item) {
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'sparepart_id' => $item['sparepart_id'],
                    'quantity' => $item['quantity'],
                    'reason' => $item['reason'] ?? $request->reason
                ]);

                $sparepart = Sparepart::find($item['sparepart_id']);

                if ($request->type === 'in') {
                    $sparepart->increment('stock', $item['quantity']);
                    StockCard::record(
                        $sparepart->id,
                        'in',
                        $item['quantity'],
                        "Penambahan stok: {$request->reason}",
                        $adjustment->id
                    );
                } elseif ($request->type === 'out') {
                    $sparepart->decrement('stock', $item['quantity']);
                    StockCard::record(
                        $sparepart->id,
                        'out',
                        $item['quantity'],
                        "Pengurangan stok: {$request->reason}",
                        $adjustment->id
                    );
                }
            }

            DB::commit();

            return redirect()->route('stock.adjustment.index')
                ->with('success', 'Penyesuaian stok berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->delete();
        return redirect()->route('stock.adjustment.index')
            ->with('success', 'Penyesuaian stok berhasil dihapus!');
    }
}
