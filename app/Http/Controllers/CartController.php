<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    public function __construct(private readonly CartService $cartService) {}

    public function index()
    {
        $cart = $this->cartService->getOrCreateCart();
        $cart->load('items.product.media', 'items.variant');

        return view('cart.index', compact('cart'));
    }

    public function store(AddToCartRequest $request): RedirectResponse
    {
        $this->cartService->addItem(
            $request->product_id,
            $request->quantity ?? 1,
            $request->variant_id,
        );

        return back()->with('success', 'Item added to cart.');
    }

    public function update(int $cartItemId, AddToCartRequest $request): RedirectResponse
    {
        $this->cartService->updateQuantity($cartItemId, $request->quantity);
        return back()->with('success', 'Cart updated.');
    }

    public function destroy(int $cartItemId): RedirectResponse
    {
        $this->cartService->removeItem($cartItemId);
        return back()->with('success', 'Item removed.');
    }
}