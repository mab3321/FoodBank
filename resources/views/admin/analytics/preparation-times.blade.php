@extends('admin.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="custome-breadcrumb">
            <h3>🕒 Preparation Time Analytics</h3>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="col-12 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Average Preparation Time --}}
            <div class="db-card bg-blue-50 border-blue-200">
                <div class="db-card-body">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="text-2xl font-bold text-blue-600">{{ number_format($stats['avg_actual_time'], 1) }}</h4>
                            <p class="text-sm text-gray-600">Avg Actual Time (min)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- On-Time Performance --}}
            <div class="db-card bg-green-50 border-green-200">
                <div class="db-card-body">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="text-2xl font-bold text-green-600">{{ number_format($stats['on_time_percentage'], 1) }}%</h4>
                            <p class="text-sm text-gray-600">On-Time Delivery</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Overdue Orders --}}
            <div class="db-card bg-red-50 border-red-200">
                <div class="db-card-body">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div>
                            <h4 class="text-2xl font-bold text-red-600">{{ $stats['overdue_count'] }}</h4>
                            <p class="text-sm text-gray-600">Overdue Orders</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total Completed --}}
            <div class="db-card bg-purple-50 border-purple-200">
                <div class="db-card-body">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-chart-bar text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="text-2xl font-bold text-purple-600">{{ $stats['total_orders'] }}</h4>
                            <p class="text-sm text-gray-600">Total Orders</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Menu Item Performance --}}
    <div class="col-12 lg:col-6 mb-4">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">🍽️ Menu Item Performance</h3>
            </div>
            <div class="db-card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Menu Item</th>
                                <th>Estimated</th>
                                <th>Avg Actual</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menuItemStats as $item)
                            <tr>
                                <td>
                                    <div class="font-semibold">{{ $item['name'] }}</div>
                                    <div class="text-sm text-gray-600">{{ $item['count'] }} orders</div>
                                </td>
                                <td>{{ $item['estimated_time'] }} min</td>
                                <td>{{ number_format($item['avg_actual'], 1) }} min</td>
                                <td>
                                    @if($item['avg_actual'] <= $item['estimated_time'])
                                        <span class="badge bg-green-100 text-green-700">✓ On Time</span>
                                    @elseif($item['avg_actual'] <= $item['estimated_time'] * 1.2)
                                        <span class="badge bg-yellow-100 text-yellow-700">⚠ Slightly Late</span>
                                    @else
                                        <span class="badge bg-red-100 text-red-700">✗ Often Late</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No data available yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Order Performance --}}
    <div class="col-12 lg:col-6 mb-4">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">📊 Recent Order Performance</h3>
            </div>
            <div class="db-card-body">
                <div class="space-y-4">
                    @forelse($recentOrders as $order)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <div class="font-semibold">Order #{{ $order->order_code }}</div>
                            <div class="text-sm text-gray-600">
                                {{ $order->ready_at ? $order->ready_at->format('M j, H:i') : 'Processing...' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold">{{ $order->actual_preparation_time ?? 'N/A' }} min</div>
                            <div class="text-sm">
                                @if($order->actual_preparation_time)
                                    @if($order->actual_preparation_time <= $order->estimated_wait_time)
                                        <span class="text-green-600">✓ On Time</span>
                                    @else
                                        <span class="text-red-600">{{ $order->actual_preparation_time - $order->estimated_wait_time }}min late</span>
                                    @endif
                                @else
                                    <span class="text-blue-600">Processing...</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-clock text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600">No recent orders with timing data</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Performance Recommendations --}}
    <div class="col-12">
        <div class="db-card">
            <div class="db-card-header">
                <h3 class="db-card-title">💡 Recommendations</h3>
            </div>
            <div class="db-card-body">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @if($stats['on_time_percentage'] < 80)
                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                        <h4 class="font-semibold text-red-800 mb-2">⚠️ Low On-Time Rate</h4>
                        <p class="text-sm text-red-700">Consider increasing estimated times for frequently late items or optimizing kitchen workflow.</p>
                    </div>
                    @endif

                    @if($stats['avg_actual_time'] > 25)
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h4 class="font-semibold text-yellow-800 mb-2">🕐 High Average Time</h4>
                        <p class="text-sm text-yellow-700">Average preparation time is high. Consider streamlining popular menu items.</p>
                    </div>
                    @endif

                    @if($stats['overdue_count'] > 0)
                    <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg">
                        <h4 class="font-semibold text-orange-800 mb-2">📋 Active Overdue</h4>
                        <p class="text-sm text-orange-700">You have {{ $stats['overdue_count'] }} overdue orders. Check the orders page.</p>
                    </div>
                    @endif

                    @if($stats['on_time_percentage'] >= 90)
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <h4 class="font-semibold text-green-800 mb-2">🎉 Excellent Performance!</h4>
                        <p class="text-sm text-green-700">Great job! Your on-time rate is excellent.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}
</style>
@endpush