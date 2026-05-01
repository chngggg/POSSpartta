<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\BarcodeService;

class Sparepart extends Model
{
    protected $table = 'spareparts';

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'description',
        'purchase_price',
        'selling_price',
        'stock',
        'min_stock',
        'unit',
        'brand',
        'location_rack',
        'is_active'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'stock' => 'integer',
        'min_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->min_stock;
    }

    /**
     * Get barcode image URL
     */
    public function getBarcodeUrlAttribute(): string
    {
        $barcodeService = new BarcodeService();
        return $barcodeService->saveToStorage($this->code, $this->code . '.png');
    }

    /**
     * Get barcode HTML for display
     */
    public function getBarcodeHtmlAttribute(): string
    {
        $barcodeService = new BarcodeService();
        return $barcodeService->generateForBlade($this->code);
    }
}
