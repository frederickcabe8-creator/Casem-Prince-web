@extends('layouts.app')
@section('title', 'Products')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- Sidebar filters --}}
        <aside class="w-full lg:w-64 shrink-0">
            <form action="{{ route('products.index') }}" method="GET" id="filter-form">

                <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-6">

                    {{-- Search (mobile) --}}
                    <div class="lg:hidden">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search products..."
                               class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>

                    {{-- Category filter --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Category</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm cursor-pointer">
                                <input type="radio" name="category" value=""
                                       {{ !request('category') ? 'checked' : '' }}
                                       class="text-indigo-600" onchange="this.form.submit()">
                                <span class="text-gray-700">All categories</span>
                            </label>
                            @foreach ($categories as $category)
                                <label class="flex items-center gap-2 text-sm cursor-pointer">
                                    <input type="radio" name="category" value="{{ $category->id }}"
                                           {{ request('category') == $category->id ? 'checked' : '' }}
                                           class="text-indigo-600" onchange="this.form.submit()">
                                    <span class="text-gray-700">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price range --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Price Range</label>
                        <div class="flex gap-2 items-center">
                            <input type="number" name="min_price" value="{{ request('min_price') }}"
                                   placeholder="Min" min="0"
                                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                            <span class="text-gray-400 text-sm">–</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}"
                                   placeholder="Max" min="0"
                                   class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <button type="submit" class="mt-3 w-full text-sm bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 transition">
                            Apply
                        </button>
                    </div>

                    {{-- Clear filters --}}
                    @if (request()->hasAny(['search', 'category', 'min_price', 'max_price', 'sort']))
                        <a href="{{ route('products.index') }}"
                           class="block text-center text-sm text-red-500 hover:underline">
                            Clear all filters
                        </a>
                    @endif
                </div>
            </form>
        </aside>

        {{-- Main content --}}
        <div class="flex-1">

            {{-- Toolbar --}}
            <div class="flex items-center justify-between mb-6">
                <p class="text-sm text-gray-500">
                    {{ $products->total() }} products found
                    @if (request('search'))
                        for <span class="font-medium text-gray-700">"{{ request('search') }}"</span>
                    @endif
                </p>
                <select name="sort" form="filter-form"
                        onchange="document.getElementById('filter-form').submit()"
                        class="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="" {{ !request('sort') ? 'selected' : '' }}>Sort: Default</option>
                    <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                </select>
            </div>

            {{-- Product grid --}}
            @if ($products->isEmpty())
                <div class="text-center py-20">
                    <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <p class="text-gray-400 text-lg">No products found.</p>
                    <a href="{{ route('products.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Browse all products</a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach ($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-10">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection