<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    public function createFromCart(array $addressData, ?string $couponCode = null): Order
    {
        $cart = $this->cartService->getOrCreateCart();

        if ($cart->items->isEmpty()) {
            throw new \RuntimeException('Cannot create order from an empty cart.');
        }

        return DB::transaction(function () use ($cart, $addressData, $couponCode) {
            $coupon   = $this->resolveCoupon($couponCode);
            $subtotal = $cart->subtotal;
            $discount = $coupon ? $this->calculateDiscount($coupon, $subtotal) : 0;
            $tax      = round(($subtotal - $discount) * 0.10, 2);
            $shipping = $subtotal >= 100 ? 0 : 9.99;
            $total    = $subtotal - $discount + $tax + $shipping;

            $order = Order::create([
                'user_id'          => Auth::id(),
                'coupon_id'        => $coupon?->id,
                'status'           => 'pending',
                'payment_status'   => 'pending',
                'currency'         => 'USD',
                'subtotal'         => $subtotal,
                'discount_amount'  => $discount,
                'tax_amount'       => $tax,
                'shipping_amount'  => $shipping,
                'total_amount'     => $total,
                'shipping_address' => $addressData['shipping'],
                'billing_address'  => $addressData['billing'] ?? $addressData['shipping'],
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id'   => $item->product_id,
                    'variant_id'   => $item->variant_id,
                    'product_name' => $item->product->name,
                    'variant_name' => $item->variant?->name,
                    'sku'          => $item->variant?->sku ?? $item->product->sku,
                    'quantity'     => $item->quantity,
                    'unit_price'   => $item->unit_price,
                    'total_price'  => $item->unit_price * $item->quantity,
                ]);

                // Decrement stock safely (won't go below 0)
                $item->product->newQuery()
                    ->where('id', $item->product->id)
                    ->where('stock_quantity', '>=', $item->quantity)
                    ->decrement('stock_quantity', $item->quantity);
            }

            $coupon?->increment('used_count');
            $this->cartService->clear();

            return $order;
        });
    }

    public function updateStatus(Order $order, string $newStatus, ?string $notes = null): void
    {
        $oldStatus = $order->status;

        $order->update(['status' => $newStatus]);

        OrderStatusHistory::create([
            'order_id'    => $order->id,
            'user_id'     => Auth::id(),
            'from_status' => $oldStatus,
            'to_status'   => $newStatus,
            'notes'       => $notes,
        ]);
    }

    private function resolveCoupon(?string $code): ?Coupon
    {
        if (!$code) {
            return null;
        }

        return Coupon::where('code', $code)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->firstOrFail();
    }

    private function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        return match ($coupon->type) {
            'percentage' => round($subtotal * ($coupon->value / 100), 2),
            'fixed'      => min($coupon->value, $subtotal),
            default      => 0,
        };
    }
}