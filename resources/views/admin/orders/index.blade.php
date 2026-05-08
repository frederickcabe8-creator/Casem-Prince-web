@extends('layouts.app')
@section('title', 'Admin — Orders')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    <div class="mb-8">
        <p class="text-sm text-indigo-600 font-medium uppercase tracking-wide">Admin Panel</p>
        <h1 class="text-3xl font-bold text-gray-900">Orders</h1>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        @php
            $pending   = \App\Models\Order::where('status', 'pending')->count();
            $confirmed = \App\Models\Order::where('status', 'confirmed')->count();
            $shipped   = \App\Models\Order::where('status', 'shipped')->count();
            $delivered = \App\Models\Order::where('status', 'delivered')->count();
        @endphp
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Pending</p>
            <p class="text-3xl font-bold text-yellow-500 mt-1">{{ $pending }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Confirmed</p>
            <p class="text-3xl font-bold text-blue-500 mt-1">{{ $confirmed }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Shipped</p>
            <p class="text-3xl font-bold text-indigo-500 mt-1">{{ $shipped }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-5">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Delivered</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $delivered }}</p>
        </div>
    </div>

    {{-- Orders table --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-900">All Orders</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Order</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Customer</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Date</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Payment</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($orders as $order)
                        @php
                            $statusColors = [
                                'pending'    => 'bg-yellow-50 text-yellow-700',
                                'confirmed'  => 'bg-blue-50 text-blue-700',
                                'processing' => 'bg-purple-50 text-purple-700',
                                'shipped'    => 'bg-indigo-50 text-indigo-700',
                                'delivered'  => 'bg-green-50 text-green-700',
                                'cancelled'  => 'bg-red-50 text-red-600',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $order->order_number }}</td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-800">{{ $order->user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $order->user->email }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ $order->placed_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">${{ number_format($order->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-50 text-gray-600' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }} font-medium">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $order) }}"
                                   class="text-xs bg-indigo-50 text-indigo-600 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition font-medium">
                                    Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-400">No orders yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection