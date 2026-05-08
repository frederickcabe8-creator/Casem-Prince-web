@extends('layouts.app')
@section('title', 'My Orders')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    <h1 class="text-3xl font-bold text-gray-900 mb-8">My Orders</h1>

    @if ($orders->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-100">
            <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-lg font-semibold text-gray-400 mb-4">No orders yet</p>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold hover:bg-indigo-700 transition">
                Start Shopping
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <div class="bg-white rounded-2xl border border-gray-100 p-6 hover:border-indigo-100 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-gray-900">{{ $order->order_number }}</span>
                                @php
                                    $statusColors = [
                                        'pending'    => 'bg-yellow-50 text-yellow-700',
                                        'confirmed'  => 'bg-blue-50 text-blue-700',
                                        'processing' => 'bg-purple-50 text-purple-700',
                                        'shipped'    => 'bg-indigo-50 text-indigo-700',
                                        'delivered'  => 'bg-green-50 text-green-700',
                                        'cancelled'  => 'bg-red-50 text-red-600',
                                        'refunded'   => 'bg-gray-50 text-gray-600',
                                    ];
                                @endphp
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-50 text-gray-600' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 mt-1">
                                {{ $order->placed_at->format('M d, Y') }} ·
                                {{ $order->items->count() }} item(s)
                            </p>
                        </div>

                        <div class="flex items-center gap-4">
                            <span class="text-lg font-bold text-gray-900">
                                ${{ number_format($order->total_amount, 2) }}
                            </span>
                            <a href="{{ route('orders.show', $order) }}"
                               class="text-sm bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-4 py-2 rounded-lg font-medium transition">
                                View Details
                            </a>
                        </div>
                    </div>

                    {{-- Item thumbnails --}}
                    <div class="flex gap-2 mt-4">
                        @foreach ($order->items->take(4) as $item)
                            <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden border border-gray-100">
                                @if ($item->product?->getFirstMediaUrl('thumbnail'))
                                    <img src="{{ $item->product->getFirstMediaUrl('thumbnail', 'thumb') }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-indigo-50 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @if ($order->items->count() > 4)
                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-xs font-semibold text-gray-500">
                                +{{ $order->items->count() - 4 }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection