<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\StockCard;
use Illuminate\Http\Request;

class StockCardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $sparepartId = $request->get('sparepart_id');
        $spareparts = Sparepart::orderBy('name')->get();

        $cards = collect();
        $selectedSparepart = null;

        if ($sparepartId) {
            $selectedSparepart = Sparepart::find($sparepartId);
            $cards = StockCard::with('sparepart')
                ->where('sparepart_id', $sparepartId)
                ->orderBy('date', 'desc')
                ->paginate(20);
        }

        return view('stock.card-index', compact('spareparts', 'cards', 'selectedSparepart'));
    }

    public function show(Sparepart $sparepart)
    {
        $cards = StockCard::where('sparepart_id', $sparepart->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('stock.card-show', compact('sparepart', 'cards'));
    }
}
