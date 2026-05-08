<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'variant_id', 'product_name',
        'variant_name', 'sku', 'quantity', 'unit_price', 'total_price', 'options',
    ];

    protected function casts(): array
    {
        return [
            'unit_price'  => 'decimal:2',
            'total_price' => 'decimal:2',
            'options'     => 'array',
        ];
    }

    public function order(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}