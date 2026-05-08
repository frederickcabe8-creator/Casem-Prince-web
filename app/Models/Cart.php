<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['user_id', 'session_id', 'expires_at'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime'];
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn ($item) => $item->unit_price * $item->quantity);
    }

    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('quantity');
    }
}