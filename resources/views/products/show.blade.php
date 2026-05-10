@extends('layouts.app')
@section('title', $product->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-8 flex items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-indigo-600">Home</a>
        <span>/</span>
        <a href="{{ route('products.index') }}" class="hover:text-indigo-600">Products</a>
        <span>/</span>
        <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="hover:text-indigo-600">
            {{ $product->category->name }}
        </a>
        <span>/</span>
        <span class="text-gray-600">{{ $product->name }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">

        {{-- Images --}}
        <div class="space-y-4">
            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-100 border border-gray-100">
                @if ($product->getFirstMediaUrl('thumbnail'))
                    <img src="{{ $product->getFirstMediaUrl('thumbnail') }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-24 h-24 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Thumbnail gallery --}}
            @if ($product->getMedia('images')->count() > 1)
                <div class="grid grid-cols-4 gap-3">
                    @foreach ($product->getMedia('images') as $media)
                        <div class="aspect-square rounded-xl overflow-hidden border-2 border-gray-100 hover:border-indigo-400 cursor-pointer transition">
                            <img src="{{ $media->getUrl('thumb') }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Product info --}}
        <div>
            <span class="text-sm font-medium text-indigo-600 uppercase tracking-wide">
                {{ $product->category->name }}
            </span>

            <h1 class="mt-2 text-3xl font-bold text-gray-900 leading-tight">
                {{ $product->name }}
            </h1>

            <p class="mt-1 text-sm text-gray-400">SKU: {{ $product->sku }}</p>

            {{-- Price --}}
            <div class="mt-5 flex items-baseline gap-3">
                <span class="text-4xl font-extrabold text-gray-900">
                    {{ formatPrice($product->effective_price) }}
                </span>
                @if ($product->is_on_sale)
                    <span class="text-xl text-gray-400 line-through">
                        {{ formatPrice($product->base_price) }}
                    </span>
                    <span class="text-sm bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">
                        Save {{ formatPrice($product->base_price - $product->sale_price) }}
                    </span>
                @endif
            </div>

            {{-- Short description --}}
            @if ($product->short_description)
                <p class="mt-4 text-gray-600 leading-relaxed">{{ $product->short_description }}</p>
            @endif

            {{-- Stock status --}}
            <div class="mt-4">
                @if ($product->is_in_stock)
                    <span class="inline-flex items-center gap-1.5 text-sm text-green-700 bg-green-50 px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                        In stock ({{ $product->stock_quantity }} available)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-sm text-red-600 bg-red-50 px-3 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                        Out of stock
                    </span>
                @endif
            </div>

            {{-- Variants --}}
            @if ($product->variants->isNotEmpty())
                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Variant</label>
                    <div class="flex flex-wrap gap-2" id="variant-selector">
                        @foreach ($product->variants as $variant)
                            <button type="button"
                                    data-variant-id="{{ $variant->id }}"
                                    data-price="{{ $variant->price ?? $product->effective_price }}"
                                    onclick="selectVariant(this)"
                                    class="variant-btn px-4 py-2 text-sm border border-gray-200 rounded-lg hover:border-indigo-500 transition {{ $loop->first ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'text-gray-700' }}">
                                {{ $variant->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Add to cart form --}}
            @if ($product->is_in_stock)
                <form action="{{ route('cart.store') }}" method="POST" class="mt-8">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="variant_id" id="selected-variant" value="{{ $product->variants->first()?->id }}">

                    <div class="flex gap-3">
                        {{-- Quantity --}}
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                            <button type="button" onclick="changeQty(-1)"
                                    class="px-4 py-3 text-gray-500 hover:bg-gray-50 transition font-bold">−</button>
                            <input type="number" name="quantity" id="qty" value="1" min="1" max="{{ $product->stock_quantity }}"
                                   class="w-14 text-center text-sm font-semibold border-x border-gray-200 py-3 outline-none">
                            <button type="button" onclick="changeQty(1)"
                                    class="px-4 py-3 text-gray-500 hover:bg-gray-50 transition font-bold">+</button>
                        </div>

                        {{-- Submit --}}
                        <button type="submit"
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition shadow-sm">
                            Add to Cart
                        </button>
                    </div>
                </form>
            @else
                <div class="mt-8">
                    <button disabled class="w-full bg-gray-100 text-gray-400 font-semibold py-3 px-6 rounded-xl cursor-not-allowed">
                        Out of Stock
                    </button>
                </div>
            @endif

            {{-- Description --}}
            @if ($product->description)
                <div class="mt-10 pt-8 border-t border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Description</h2>
                    <div class="prose prose-sm text-gray-600 leading-relaxed">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Related products --}}
    @if ($related->isNotEmpty())
        <section class="mt-20">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">You might also like</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach ($related as $relatedProduct)
                    <x-product-card :product="$relatedProduct" />
                @endforeach
            </div>
        </section>
    @endif

</div>

@push('scripts')
<script>
function changeQty(delta) {
    const input = document.getElementById('qty');
    const newVal = Math.max(1, Math.min({{ $product->stock_quantity }}, parseInt(input.value) + delta));
    input.value = newVal;
}

function selectVariant(btn) {
    document.querySelectorAll('.variant-btn').forEach(b => {
        b.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
        b.classList.add('border-gray-200', 'text-gray-700');
    });
    btn.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
    btn.classList.remove('border-gray-200', 'text-gray-700');
    document.getElementById('selected-variant').value = btn.dataset.variantId;
}
</script>
@endpush

@endsection