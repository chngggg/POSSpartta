<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAdjustmentItem extends Model
{
    protected $fillable = [
        'stock_adjustment_id',
        'sparepart_id',
        'quantity',
        'reason'
    ];

    public function stockAdjustment(): BelongsTo
    {
        return $this->belongsTo(StockAdjustment::class);
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }
}
