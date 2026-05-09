@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <p class="text-sm text-indigo-600 font-medium uppercase tracking-wide">Admin Panel</p>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-sm text-gray-400 mt-1">{{ now()->format('l, F d Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.products.create') }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition">
                + Add Product
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="bg-white border border-gray-200 hover:border-indigo-400 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold transition">
                View Orders
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

        {{-- Total Revenue --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-green-50 rounded-bl-full"></div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Revenue</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">${{ number_format($totalRevenue, 2) }}</p>
            <div class="flex items-center gap-1 mt-2">
                <span class="{{ $revenueGrowth >= 0 ? 'text-green-500' : 'text-red-500' }} text-xs font-semibold">
                    {{ $revenueGrowth >= 0 ? '↑' : '↓' }} {{ abs($revenueGrowth) }}%
                </span>
                <span class="text-xs text-gray-400">vs last month</span>
            </div>
            <div class="mt-3 w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>

        {{-- Total Orders --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-blue-50 rounded-bl-full"></div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Orders</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalOrders) }}</p>
            <div class="flex items-center gap-1 mt-2">
                <span class="text-blue-500 text-xs font-semibold">{{ $ordersThisMonth }} this month</span>
            </div>
            <div class="mt-3 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
        </div>

        {{-- Total Customers --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-purple-50 rounded-bl-full"></div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Customers</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalCustomers) }}</p>
            <div class="flex items-center gap-1 mt-2">
                <span class="text-purple-500 text-xs font-semibold">Registered users</span>
            </div>
            <div class="mt-3 w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>

        {{-- Total Products --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-orange-50 rounded-bl-full"></div>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Products</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ number_format($totalProducts) }}</p>
            <div class="flex items-center gap-1 mt-2">
                <span class="text-orange-500 text-xs font-semibold">Active listings</span>
            </div>
            <div class="mt-3 w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v10l-8 4m0-10L4 7m8 4v10"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Revenue Line Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="font-bold text-gray-900">Revenue Overview</h2>
                    <p class="text-sm text-gray-400">Last 30 days</p>
                </div>
                <span class="text-2xl font-bold text-indigo-600">${{ number_format($revenueThisMonth, 2) }}</span>
            </div>
            <canvas id="revenueChart" height="120"></canvas>
        </div>

        {{-- Order Status Donut --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 mb-2">Order Status</h2>
            <p class="text-sm text-gray-400 mb-6">Current breakdown</p>
            <canvas id="statusChart" height="200"></canvas>
            <div class="mt-4 space-y-2">
                @foreach($ordersByStatus as $status => $count)
                    @php
                        $colors = [
                            'pending'    => 'bg-yellow-400',
                            'confirmed'  => 'bg-blue-400',
                            'processing' => 'bg-purple-400',
                            'shipped'    => 'bg-indigo-400',
                            'delivered'  => 'bg-green-400',
                            'cancelled'  => 'bg-red-400',
                            'refunded'   => 'bg-gray-400',
                        ];
                    @endphp
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full {{ $colors[$status] ?? 'bg-gray-400' }}"></div>
                            <span class="text-gray-600 capitalize">{{ $status }}</span>
                        </div>
                        <span class="font-semibold text-gray-900">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Monthly Bar Chart + Payment Methods --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Monthly Revenue Bar Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 mb-1">Monthly Revenue</h2>
            <p class="text-sm text-gray-400 mb-6">Last 6 months comparison</p>
            <canvas id="monthlyChart" height="120"></canvas>
        </div>

        {{-- Payment Methods --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="font-bold text-gray-900 mb-2">Payment Methods</h2>
            <p class="text-sm text-gray-400 mb-6">Revenue by method</p>
            @if($paymentMethods->isEmpty())
                <p class="text-gray-400 text-sm text-center py-8">No paid orders yet</p>
            @else
                <div class="space-y-4">
                    @foreach($paymentMethods as $method)
                        @php
                            $icons = ['stripe' => '💳', 'cod' => '💵', 'paypal' => '🅿️', 'gcash' => '📱'];
                            $icon = $icons[$method->payment_method] ?? '💰';
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">{{ $icon }}</span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 capitalize">{{ $method->payment_method ?? 'Unknown' }}</p>
                                    <p class="text-xs text-gray-400">{{ $method->count }} orders</p>
                                </div>
                            </div>
                            <span class="font-bold text-gray-900">${{ number_format($method->total, 2) }}</span>
                        </div>
                        @php
                            $totalPaid = $paymentMethods->sum('total');
                            $percent = $totalPaid > 0 ? ($method->total / $totalPaid) * 100 : 0;
                        @endphp
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Top Products + Recent Orders --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top Products --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Top Selling Products</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($topProducts as $index => $product)
                    <div class="flex items-center gap-4 px-6 py-4">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold flex items-center justify-center">
                            {{ $index + 1 }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $product->name }}</p>
                            <p class="text-xs text-gray-400">{{ $product->total_sold }} units sold</p>
                        </div>
                        <span class="font-bold text-gray-900 text-sm">${{ number_format($product->total_revenue, 2) }}</span>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-gray-400 text-sm">No sales data yet</div>
                @endforelse
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-900">Recent Orders</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-indigo-600 hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($recentOrders as $order)
                    @php
                        $statusColors = [
                            'pending'   => 'bg-yellow-50 text-yellow-700',
                            'confirmed' => 'bg-blue-50 text-blue-700',
                            'shipped'   => 'bg-indigo-50 text-indigo-700',
                            'delivered' => 'bg-green-50 text-green-700',
                            'cancelled' => 'bg-red-50 text-red-600',
                        ];
                    @endphp
                    <div class="flex items-center gap-3 px-6 py-3">
                        <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">
                            <span class="text-xs font-bold text-indigo-600">{{ substr($order->user->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $order->user->name }}</p>
                            <p class="text-xs text-gray-400">{{ $order->order_number }}</p>
                        </div>
                        <div class="text-right shrink-0">
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Line Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($last30Days->pluck('date')) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($last30Days->pluck('revenue')) !!},
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99,102,241,0.08)',
                borderWidth: 2,
                pointRadius: 0,
                pointHoverRadius: 5,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { maxTicksLimit: 8, font: { size: 11 } } },
                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 }, callback: v => '$' + v } }
            }
        }
    });

    // Order Status Donut Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($ordersByStatus->keys()->map(fn($s) => ucfirst($s))) !!},
            datasets: [{
                data: {!! json_encode($ordersByStatus->values()) !!},
                backgroundColor: ['#facc15','#60a5fa','#a78bfa','#818cf8','#34d399','#f87171','#9ca3af'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });

    // Monthly Bar Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyRevenue->pluck('month')) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($monthlyRevenue->pluck('revenue')) !!},
                backgroundColor: 'rgba(99,102,241,0.8)',
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 11 }, callback: v => '$' + v } }
            }
        }
    });
</script>
@endpush