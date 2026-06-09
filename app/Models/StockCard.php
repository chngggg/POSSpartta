<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockCard extends Model
{
    protected $table = 'stock_cards';

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
}
