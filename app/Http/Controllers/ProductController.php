<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::active()
            ->with(['category', 'media'])
            ->when($request->search, fn ($q) => $q->search($request->search))
            ->when($request->category, fn ($q) => $q->inCategory($request->category))
            ->when(
                $request->min_price && $request->max_price,
                fn ($q) => $q->priceBetween($request->min_price, $request->max_price)
            )
            ->when($request->sort === 'price_asc', fn ($q) => $q->orderBy('base_price'))
            ->when($request->sort === 'price_desc', fn ($q) => $q->orderByDesc('base_price'))
            ->when($request->sort === 'newest', fn ($q) => $q->latest())
            ->paginate(16)->withQueryString();

        $categories = Category::active()->whereNull('parent_id')->with('children')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'variants', 'media']);

        $related = Product::active()
            ->inCategory($product->category_id)
            ->where('id', '!=', $product->id)
            ->limit(4)->get();

        return view('products.show', compact('product', 'related'));
    }
}