@extends('layouts.app')
@section('title', 'Your Cart')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-gray-900 mb-8">Your Cart</h1>

    @if ($cart->items->isEmpty())
        {{-- Empty cart --}}
        <div class="text-center py-24 bg-white rounded-2xl border border-gray-100">
            <svg class="w-20 h-20 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-1.5 6h13M7 13L5.4 5M10 21a1 1 0 1 0 2 0 1 1 0 0 0-2 0zm7 0a1 1 0 1 0 2 0 1 1 0 0 0-2 0z"/>
            </svg>
            <p class="text-xl font-semibold text-gray-400 mb-2">Your cart is empty</p>
            <p class="text-sm text-gray-400 mb-6">Looks like you haven't added anything yet.</p>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition">
                Browse Products
            </a>
        </div>

    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Cart items --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach ($cart->items as $item)
                    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex gap-5">

                        {{-- Product image --}}
                        <div class="w-24 h-24 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                            @if ($item->product->getFirstMediaUrl('thumbnail'))
                                <img src="{{ $item->product->getFirstMediaUrl('thumbnail', 'thumb') }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-indigo-50">
                                    <svg class="w-8 h-8 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                              d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <a href="{{ route('products.show', $item->product) }}"
                                       class="font-semibold text-gray-900 hover:text-indigo-600 line-clamp-1">
                                        {{ $item->product->name }}
                                    </a>
                                    @if ($item->variant)
                                        <p class="text-sm text-gray-400 mt-0.5">{{ $item->variant->name }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500 mt-1">
                                        ${{ number_format($item->unit_price, 2) }} each
                                    </p>
                                </div>
                                {{-- Remove --}}
                                <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-300 hover:text-red-500 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            {{-- Quantity + line total --}}
                            <div class="flex items-center justify-between mt-4">
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                        <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}"
                                                class="px-3 py-1.5 text-gray-500 hover:bg-gray-50 text-sm font-bold">−</button>
                                        <span class="px-4 py-1.5 text-sm font-semibold border-x border-gray-200">
                                            {{ $item->quantity }}
                                        </span>
                                        <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}"
                                                class="px-3 py-1.5 text-gray-500 hover:bg-gray-50 text-sm font-bold">+</button>
                                    </div>
                                </form>

                                <span class="font-bold text-gray-900">
                                    ${{ number_format($item->unit_price * $item->quantity, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Continue shopping --}}
                <div class="pt-2">
                    <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 hover:underline">
                        ← Continue Shopping
                    </a>
                </div>
            </div>

            {{-- Order summary --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-24">
                    <h2 class="text-lg font-bold text-gray-900 mb-5">Order Summary</h2>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal ({{ $cart->total_items }} items)</span>
                            <span class="font-medium">${{ number_format($cart->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Shipping</span>
                            <span class="font-medium text-green-600">
                                {{ $cart->subtotal >= 100 ? 'FREE' : '$9.99' }}
                            </span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Tax (10%)</span>
                            <span class="font-medium">${{ number_format($cart->subtotal * 0.10, 2) }}</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 mt-4 pt-4">
                        <div class="flex justify-between font-bold text-gray-900 text-base">
                            <span>Total</span>
                            <span>
                                @php
                                    $shipping = $cart->subtotal >= 100 ? 0 : 9.99;
                                    $tax = $cart->subtotal * 0.10;
                                    $total = $cart->subtotal + $shipping + $tax;
                                @endphp
                                ${{ number_format($total, 2) }}
                            </span>
                        </div>
                    </div>

                    <a href="{{ route('checkout.index') }}"
                       class="mt-6 block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center font-semibold py-3 rounded-xl transition shadow-sm">
                        Proceed to Checkout →
                    </a>

                    {{-- Security badges --}}
                    <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Secure checkout
                    </div>
                </div>
            </div>

        </div>
    @endif
</div>
@endsection