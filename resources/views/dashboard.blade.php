@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Welcome, {{ auth()->user()->name }}!</h1>
    <p class="text-gray-500 mb-8">Here's your account overview.</p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 text-center">
            <p class="text-3xl font-extrabold text-indigo-600">{{ auth()->user()->orders()->count() }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Orders</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6 text-center">
            <p class="text-3xl font-extrabold text-green-600">${{ number_format(auth()->user()->orders()->where('payment_status', 'paid')->sum('total_amount'), 2) }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Spent</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6 text-center">
            <p class="text-3xl font-extrabold text-purple-600">{{ auth()->user()->orders()->where('status', 'delivered')->count() }}</p>
            <p class="text-sm text-gray-500 mt-1">Delivered</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Recent Orders</h2>
            <a href="{{ route('orders.index') }}" class="text-xs text-indigo-600 hover:underline">View all →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse(auth()->user()->orders()->latest()->limit(5)->get() as $order)
                @php
                    $statusColors = [
                        'pending'   => 'bg-yellow-50 text-yellow-700',
                        'confirmed' => 'bg-blue-50 text-blue-700',
                        'shipped'   => 'bg-indigo-50 text-indigo-700',
                        'delivered' => 'bg-green-50 text-green-700',
                        'cancelled' => 'bg-red-50 text-red-600',
                    ];
                @endphp
                <div class="flex items-center justify-between px-6 py-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">${{ number_format($order->total_amount, 2) }}</p>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-50 text-gray-600' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center text-gray-400 text-sm">No orders yet</div>
            @endforelse
        </div>
    </div>
</div>
@endsection