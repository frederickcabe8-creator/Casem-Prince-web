<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'media'])
            ->when($request->search, fn ($q) => $q->search($request->search))
            ->latest()->paginate(15);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'sku'               => 'required|string|unique:products,sku',
            'base_price'        => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'stock_quantity'    => 'required|integer|min:0',
            'images.*'          => 'nullable|image|max:2048',
        ]);

        $data['slug']        = Str::slug($request->name);
        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $product = Product::create($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('thumbnail');
            }
        }

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::active()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'category_id'       => 'required|exists:categories,id',
            'name'              => 'required|string|max:255',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'sku'               => 'required|string|unique:products,sku,' . $product->id,
            'base_price'        => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'stock_quantity'    => 'required|integer|min:0',
            'images.*'          => 'nullable|image|max:2048',
        ]);

        $data['slug']        = Str::slug($request->name);
        $data['is_active']   = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');

        $product->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('thumbnail');
            }
        }

        return redirect()->route('admin.products.index')
                         ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')
                         ->with('success', 'Product deleted.');
    }
}