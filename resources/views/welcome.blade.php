@extends('layouts.app')
@section('title', 'Home')

@section('content')

    {{-- Hero --}}
    <section class="relative text-white overflow-hidden" style="height: 500px;">
        {{-- GIF Background --}}
        <img src="{{ asset('images/banner.gif') }}"
             alt="Banner"
             class="absolute inset-0 w-full h-full object-cover">

        {{-- Dark overlay --}}
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        {{-- Content --}}
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
            <h1 class="text-5xl font-extrabold tracking-tight leading-tight">
                Shop the latest <br class="hidden sm:block"> products
            </h1>
            <p class="mt-4 text-lg text-gray-200 max-w-xl mx-auto">
                Discover thousands of products at unbeatable prices. Free shipping on orders over $100.
            </p>
            <a href="{{ route('products.index') }}"
               class="mt-8 inline-block bg-white text-indigo-700 font-semibold px-8 py-3 rounded-full hover:bg-indigo-50 transition shadow-lg">
                Shop Now
            </a>
        </div>
    </section>

    {{-- Featured products --}}
    <section class="max-w-7xl mx-auto px-4 py-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Featured Products</h2>
            <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:underline">View all →</a>
        </div>

        @php
            $featured = \App\Models\Product::active()->featured()->with(['category', 'media'])->limit(8)->get();
        @endphp

        @if ($featured->isEmpty())
            <p class="text-gray-400 text-center py-12">No featured products yet.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($featured as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        @endif
    </section>

    {{-- Categories --}}
    <section class="bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 py-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Shop by Category</h2>

            @php
                $categories = \App\Models\Category::active()->whereNull('parent_id')->limit(6)->get();
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach ($categories as $category)
                    <a href="{{ route('products.index', ['category' => $category->id]) }}"
                       class="flex flex-col items-center p-4 rounded-2xl border border-gray-100 hover:border-indigo-200 hover:bg-indigo-50 transition group">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center mb-3 group-hover:bg-indigo-200 transition">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-gray-700 text-center group-hover:text-indigo-600">
                            {{ $category->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

@endsection