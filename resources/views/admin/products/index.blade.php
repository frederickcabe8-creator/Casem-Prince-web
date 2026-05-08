@extends('layouts.app')
@section('title', 'Admin — Products')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm text-indigo-600 font-medium uppercase tracking-wide">Admin Panel</p>
            <h1 class="text-3xl font-bold text-gray-900">Products</h1>
        </div>
        <a href="{{ route('admin.products.create') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm transition shadow-sm">
            + Add Product
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        @php
            $total    = \App\Models\Product::count();
            $active   = \App\Models\Product::where('is_active', true)->count();
            $featured = \App\Models\Product::where('is_featured', true)->count();
            $outstock = \App\Models\Product::where('stock_quantity', 0)->count();
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Total</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Active</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $active }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Featured</p>
            <p class="text-3xl font-bold text-indigo-600 mt-1">{{ $featured }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Out of Stock</p>
            <p class="text-3xl font-bold text-red-500 mt-1">{{ $outstock }}</p>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-900">All Products</h2>
            <form action="{{ route('admin.products.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search products..."
                       class="text-sm px-3 py-1.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                <button type="submit" class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-lg transition">Search</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Product</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">SKU</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Category</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Price</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Stock</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($products as $product)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-50 overflow-hidden shrink-0">
                                        @if ($product->getFirstMediaUrl('thumbnail'))
                                            <img src="{{ $product->getFirstMediaUrl('thumbnail', 'thumb') }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                        @if ($product->is_featured)
                                            <span class="text-xs text-indigo-600">Featured</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-mono text-xs">{{ $product->sku }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $product->category->name ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900">${{ number_format($product->effective_price, 2) }}</span>
                                @if ($product->is_on_sale)
                                    <span class="text-xs text-gray-400 line-through ml-1">${{ number_format($product->base_price, 2) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="{{ $product->stock_quantity == 0 ? 'text-red-500' : 'text-gray-700' }} font-medium">
                                    {{ $product->stock_quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($product->is_active)
                                    <span class="text-xs bg-green-50 text-green-700 px-2.5 py-0.5 rounded-full font-medium">Active</span>
                                @else
                                    <span class="text-xs bg-gray-100 text-gray-500 px-2.5 py-0.5 rounded-full font-medium">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="text-xs bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition font-medium">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                          onsubmit="return confirm('Delete this product?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-3 py-1.5 rounded-lg transition font-medium">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                No products found.
                                <a href="{{ route('admin.products.create') }}" class="text-indigo-600 hover:underline ml-1">Add one →</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $products->links() }}
            </div>
        @endif
    </div>
</div>
@endsection