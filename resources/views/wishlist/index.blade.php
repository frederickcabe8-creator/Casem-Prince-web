@extends('layouts.app')
@section('title', 'My Wishlist')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold text-gray-900 mb-8">My Wishlist</h1>

    @if($wishlist->isEmpty())
        <div class="text-center py-20">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <p class="text-gray-400 text-lg">Your wishlist is empty.</p>
            <a href="{{ route('products.index') }}" class="mt-4 inline-block text-sm text-indigo-600 hover:underline">Browse products</a>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($wishlist as $item)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden group">
                    <a href="{{ route('products.show', $item->product) }}" class="block aspect-square overflow-hidden bg-gray-100">
                        @if($item->product->getFirstMediaUrl('thumbnail'))
                            <img src="{{ $item->product->getFirstMediaUrl('thumbnail') }}"
                                 alt="{{ $item->product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50 to-purple-50">
                                <svg class="w-16 h-16 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                                </svg>
                            </div>
                        @endif
                    </a>
                    <div class="p-4">
                        <span class="text-xs text-indigo-600 font-medium uppercase tracking-wide">
                            {{ $item->product->category->name ?? '' }}
                        </span>
                        <a href="{{ route('products.show', $item->product) }}">
                            <h3 class="mt-1 text-sm font-semibold text-gray-900 hover:text-indigo-600 line-clamp-2">
                                {{ $item->product->name }}
                            </h3>
                        </a>
                        <p class="mt-2 text-lg font-bold text-gray-900">
                            {{ formatPrice($item->product->effective_price) }}
                        </p>
                        <div class="mt-3 flex gap-2">
                            <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit"
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 rounded-xl transition">
                                    Add to Cart
                                </button>
                            </form>
                            <form action="{{ route('wishlist.destroy', $item->product) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-400 hover:text-red-600 border border-gray-200 rounded-xl transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection