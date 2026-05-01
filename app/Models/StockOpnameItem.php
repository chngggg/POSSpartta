<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    protected $fillable = [
        'stock_opname_id',
        'sparepart_id',
        'system_stock',
        'physical_stock',
        'difference',
        'notes',
        'is_counted'
    ];

    protected $casts = [
        'is_counted' => 'boolean',
    ];

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }
}
