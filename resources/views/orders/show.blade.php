@extends('layouts.app')
@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('orders.index') }}" class="text-sm text-indigo-600 hover:underline">← My Orders</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ $order->order_number }}</h1>
            <p class="text-sm text-gray-400 mt-1">Placed on {{ $order->placed_at->format('F d, Y \a\t g:i A') }}</p>
        </div>
        @php
            $statusColors = [
                'pending'    => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                'confirmed'  => 'bg-blue-50 text-blue-700 border-blue-200',
                'processing' => 'bg-purple-50 text-purple-700 border-purple-200',
                'shipped'    => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                'delivered'  => 'bg-green-50 text-green-700 border-green-200',
                'cancelled'  => 'bg-red-50 text-red-600 border-red-200',
            ];
        @endphp
        <span class="text-sm font-semibold px-4 py-1.5 rounded-full border {{ $statusColors[$order->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
            {{ ucfirst($order->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Order items --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-900">Items Ordered</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach ($order->items as $item)
                        <div class="flex gap-4 p-5">
                            <div class="w-16 h-16 rounded-xl bg-gray-100 overflow-hidden shrink-0">
                                @if ($item->product?->getFirstMediaUrl('thumbnail'))
                                    <img src="{{ $item->product->getFirstMediaUrl('thumbnail', 'thumb') }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-indigo-50"></div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                                @if ($item->variant_name)
                                    <p class="text-sm text-gray-400">{{ $item->variant_name }}</p>
                                @endif
                                <p class="text-sm text-gray-400 mt-1">SKU: {{ $item->sku }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="font-semibold text-gray-900">${{ number_format($item->total_price, 2) }}</p>
                                <p class="text-sm text-gray-400">${{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Status timeline --}}
            @if ($order->statusHistory->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 mb-5">Order Timeline</h2>
                    <div class="space-y-4">
                        @foreach ($order->statusHistory as $history)
                            <div class="flex gap-4">
                                <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        Status changed to <span class="text-indigo-600">{{ ucfirst($history->to_status) }}</span>
                                    </p>
                                    @if ($history->notes)
                                        <p class="text-sm text-gray-400">{{ $history->notes }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $history->created_at->format('M d, Y g:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Summary sidebar --}}
        <div class="space-y-4">

            {{-- Payment summary --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-bold text-gray-900 mb-4">Payment</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if ($order->discount_amount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>−${{ number_format($order->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span>
                        <span>{{ $order->shipping_amount == 0 ? 'FREE' : '$'.number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax</span>
                        <span>${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-2 flex justify-between font-bold text-gray-900">
                        <span>Total</span>
                        <span>${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-400">
                        Payment: <span class="font-medium text-gray-600">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        Status:
                        <span class="{{ $order->isPaid() ? 'text-green-600' : 'text-yellow-600' }} font-medium">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>
                </div>
            </div>

            {{-- Shipping address --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-bold text-gray-900 mb-4">Shipping Address</h2>
                <address class="text-sm text-gray-600 not-italic leading-relaxed">
                    {{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}<br>
                    {{ $order->shipping_address['address_line_1'] }}<br>
                    @if (!empty($order->shipping_address['address_line_2']))
                        {{ $order->shipping_address['address_line_2'] }}<br>
                    @endif
                    {{ $order->shipping_address['city'] }},
                    {{ $order->shipping_address['state'] ?? '' }}
                    {{ $order->shipping_address['postal_code'] }}<br>
                    {{ $order->shipping_address['country'] }}
                </address>
            </div>

        </div>
    </div>
</div>
@endsection