<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockCard extends Model
{
    protected $fillable = [
        'sparepart_id',
        'date',
        'reference_type',
        'reference_id',
        'beginning_stock',
        'stock_in',
        'stock_out',
        'ending_stock',
        'description'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }

    public static function record($sparepartId, $type, $quantity, $description = null, $referenceId = null)
    {
        $lastCard = self::where('sparepart_id', $sparepartId)
            ->orderBy('date', 'desc')
            ->first();

        $beginningStock = $lastCard ? $lastCard->ending_stock : Sparepart::find($sparepartId)->stock;

        $stockIn = ($type === 'in') ? $quantity : 0;
        $stockOut = ($type === 'out') ? $quantity : 0;
        $endingStock = $beginningStock + $stockIn - $stockOut;

        return self::create([
            'sparepart_id' => $sparepartId,
            'date' => now(),
            'reference_type' => $type,
            'reference_id' => $referenceId,
            'beginning_stock' => $beginningStock,
            'stock_in' => $stockIn,
            'stock_out' => $stockOut,
            'ending_stock' => $endingStock,
            'description' => $description,
        ]);
    }
}
