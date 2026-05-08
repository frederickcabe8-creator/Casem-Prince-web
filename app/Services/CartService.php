<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    public function getOrCreateCart(): Cart
    {
        if (Auth::check()) {
            return Cart::firstOrCreate(
                ['user_id' => Auth::id()],
                ['user_id' => Auth::id()]
            );
        }

        $sessionId = Session::getId();
        return Cart::firstOrCreate(
            ['session_id' => $sessionId],
            ['session_id' => $sessionId, 'expires_at' => now()->addDays(7)]
        );
    }

    public function addItem(int $productId, int $quantity = 1, ?int $variantId = null): CartItem
    {
        $cart    = $this->getOrCreateCart();
        $product = Product::findOrFail($productId);
        $price   = $this->resolvePrice($product, $variantId);

        $item = CartItem::where([
            'cart_id'    => $cart->id,
            'product_id' => $productId,
            'variant_id' => $variantId,
        ])->first();

        if ($item) {
            $item->increment('quantity', $quantity);
            return $item->fresh();
        }

        return CartItem::create([
            'cart_id'    => $cart->id,
            'product_id' => $productId,
            'variant_id' => $variantId,
            'quantity'   => $quantity,
            'unit_price' => $price,
        ]);
    }

    public function updateQuantity(int $cartItemId, int $quantity): void
    {
        $cart = $this->getOrCreateCart();
        $item = CartItem::where('cart_id', $cart->id)->findOrFail($cartItemId);

        if ($quantity <= 0) {
            $item->delete();
            return;
        }

        $item->update(['quantity' => $quantity]);
    }

    public function removeItem(int $cartItemId): void
    {
        $cart = $this->getOrCreateCart();
        CartItem::where('cart_id', $cart->id)->findOrFail($cartItemId)->delete();
    }

    public function clear(): void
    {
        $this->getOrCreateCart()->items()->delete();
    }

    public function mergeGuestCart(string $sessionId): void
    {
        $guestCart = Cart::where('session_id', $sessionId)->first();
        if (!$guestCart) {
            return;
        }

        foreach ($guestCart->items as $guestItem) {
            $this->addItem($guestItem->product_id, $guestItem->quantity, $guestItem->variant_id);
        }

        $guestCart->delete();
    }

    private function resolvePrice(Product $product, ?int $variantId): float
    {
        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            return $variant->price ?? $product->effective_price;
        }

        return $product->effective_price;
    }
}