<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id', 'product_id', 'variant_id', 'quantity', 'unit_price', 'options',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'options'    => 'array',
        ];
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function getLineTotalAttribute(): string
    {
        return number_format($this->unit_price * $this->quantity, 2);
    }
}