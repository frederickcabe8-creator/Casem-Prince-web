@extends('layouts.app')
@section('title', 'Manage Order')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-indigo-600 hover:underline">← All Orders</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-1">{{ $order->order_number }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ $order->placed_at->format('F d, Y \a\t g:i A') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 space-y-6">

            {{-- Update status --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-bold text-gray-900 mb-4">Update Order Status</h2>
                <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="flex gap-3">
                    @csrf
                    @method('PUT')
                    <select name="status"
                            class="flex-1 px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        @foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'] as $status)
                            <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="notes" placeholder="Notes (optional)"
                           class="flex-1 px-4 py-2.5 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                        Update
                    </button>
                </form>
            </div>

            {{-- Items --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-bold text-gray-900">Items Ordered</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    @foreach ($order->items as $item)
                        <div class="flex gap-4 p-5">
                            <div class="w-14 h-14 rounded-xl bg-gray-100 overflow-hidden shrink-0">
                                @if ($item->product?->getFirstMediaUrl('thumbnail'))
                                    <img src="{{ $item->product->getFirstMediaUrl('thumbnail', 'thumb') }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-indigo-50"></div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-400">SKU: {{ $item->sku }} · Qty: {{ $item->quantity }}</p>
                            </div>
                            <p class="font-bold text-gray-900">${{ number_format($item->total_price, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Timeline --}}
            @if ($order->statusHistory->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-6">
                    <h2 class="font-bold text-gray-900 mb-4">Status Timeline</h2>
                    <div class="space-y-3">
                        @foreach ($order->statusHistory as $history)
                            <div class="flex gap-3">
                                <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        → <span class="text-indigo-600">{{ ucfirst($history->to_status) }}</span>
                                    </p>
                                    @if ($history->notes)
                                        <p class="text-xs text-gray-400">{{ $history->notes }}</p>
                                    @endif
                                    <p class="text-xs text-gray-400">{{ $history->created_at->format('M d, Y g:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-bold text-gray-900 mb-4">Customer</h2>
                <p class="font-medium text-gray-800">{{ $order->user->name }}</p>
                <p class="text-sm text-gray-400">{{ $order->user->email }}</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-bold text-gray-900 mb-4">Payment Summary</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span><span>${{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping</span><span>${{ number_format($order->shipping_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Tax</span><span>${{ number_format($order->tax_amount, 2) }}</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between font-bold text-gray-900">
                        <span>Total</span><span>${{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="font-bold text-gray-900 mb-3">Shipping Address</h2>
                <address class="text-sm text-gray-600 not-italic leading-relaxed">
                    {{ $order->shipping_address['first_name'] }} {{ $order->shipping_address['last_name'] }}<br>
                    {{ $order->shipping_address['address_line_1'] }}<br>
                    {{ $order->shipping_address['city'] }}, {{ $order->shipping_address['postal_code'] }}<br>
                    {{ $order->shipping_address['country'] }}
                </address>
            </div>
        </div>
    </div>
</div>
@endsection