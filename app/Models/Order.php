<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'coupon_id', 'order_number', 'status', 'payment_status',
        'payment_method', 'payment_intent_id', 'currency',
        'subtotal', 'tax_amount', 'shipping_amount', 'discount_amount', 'total_amount',
        'shipping_address', 'billing_address', 'notes', 'placed_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'billing_address'  => 'array',
            'subtotal'         => 'decimal:2',
            'tax_amount'       => 'decimal:2',
            'shipping_amount'  => 'decimal:2',
            'discount_amount'  => 'decimal:2',
            'total_amount'     => 'decimal:2',
            'placed_at'        => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->order_number ??= static::generateOrderNumber();
        });
    }

    public static function generateOrderNumber(): string
    {
        return 'ORD-' . date('Y') . '-' . str_pad(
            (static::whereYear('created_at', date('Y'))->count() + 1),
            6, '0', STR_PAD_LEFT
        );
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->latest();
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
}