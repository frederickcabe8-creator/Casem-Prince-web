<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'name', 'sku', 'price', 'stock_quantity', 'attributes'];

    protected function casts(): array
    {
        return [
            'price'      => 'decimal:2',
            'attributes' => 'array',
        ];
    }

    public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}