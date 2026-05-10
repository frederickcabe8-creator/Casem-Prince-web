<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::with('product.media', 'product.category')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('wishlist.index', compact('wishlist'));
    }

    public function toggle(Product $product): RedirectResponse
    {
        $existing = Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->delete();
            return back()->with('success', 'Removed from wishlist.');
        }

        Wishlist::create([
            'user_id'    => Auth::id(),
            'product_id' => $product->id,
        ]);

        return back()->with('success', 'Added to wishlist!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->delete();

        return back()->with('success', 'Removed from wishlist.');
    }
}