<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseReceiptItem extends Model
{
    protected $fillable = [
        'purchase_receipt_id',
        'sparepart_id',
        'quantity',
        'purchase_price'
    ];

    public function purchaseReceipt(): BelongsTo
    {
        return $this->belongsTo(PurchaseReceipt::class);
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }
}
