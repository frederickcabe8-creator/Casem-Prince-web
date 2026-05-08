@props(['product'])

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow group overflow-hidden">

    {{-- Product image --}}
    <a href="{{ route('products.show', $product) }}" class="block overflow-hidden aspect-square bg-gray-100">
        @if ($product->getFirstMediaUrl('thumbnail'))
            <img
                src="{{ $product->getFirstMediaUrl('thumbnail') }}"
                alt="{{ $product->name }}"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            >
        @else
            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-indigo-50 to-purple-50">
                <svg class="w-16 h-16 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
        @endif
    </a>

    <div class="p-4">
        {{-- Category badge --}}
        <span class="text-xs text-indigo-600 font-medium uppercase tracking-wide">
            {{ $product->category->name ?? '' }}
        </span>

        {{-- Name --}}
        <a href="{{ route('products.show', $product) }}">
            <h3 class="mt-1 text-sm font-semibold text-gray-900 hover:text-indigo-600 line-clamp-2 leading-snug">
                {{ $product->name }}
            </h3>
        </a>

        {{-- Price --}}
        <div class="mt-3 flex items-center justify-between">
            <div class="flex items-baseline gap-2">
                <span class="text-lg font-bold text-gray-900">
                    ${{ number_format($product->effective_price, 2) }}
                </span>
                @if ($product->is_on_sale)
                    <span class="text-sm text-gray-400 line-through">
                        ${{ number_format($product->base_price, 2) }}
                    </span>
                @endif
            </div>

            {{-- Stock badge --}}
            @if ($product->is_in_stock)
                <span class="text-xs text-green-600 bg-green-50 px-2 py-0.5 rounded-full">In stock</span>
            @else
                <span class="text-xs text-red-500 bg-red-50 px-2 py-0.5 rounded-full">Sold out</span>
            @endif
        </div>

        {{-- Add to cart --}}
        @if ($product->is_in_stock)
            <form action="{{ route('cart.store') }}" method="POST" class="mt-3">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium py-2 rounded-xl transition">
                    Add to cart
                </button>
            </form>
        @else
            <button disabled class="mt-3 w-full bg-gray-100 text-gray-400 text-sm font-medium py-2 rounded-xl cursor-not-allowed">
                Unavailable
            </button>
        @endif
    </div>
</div>