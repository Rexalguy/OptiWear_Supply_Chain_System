<x-filament-panels::page>
    <style>
        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            color: white;
            transition: transform 0.3s ease;
        }
        
        .metric-card:hover {
            transform: translateY(-4px);
        }
        
        .chart-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #f3f4f6;
            overflow: hidden;
        }
        
        .chart-header {
            background: linear-gradient(90deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .progress-bar {
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #8b5cf6);
            transition: width 0.8s ease;
        }
        
        @media (prefers-color-scheme: dark) {
            .chart-card {
                background-color: #1e293b;
                border-color: #334155;
            }
            .chart-header {
                background: linear-gradient(90deg, #1e293b 0%, #334155 100%);
                border-color: #475569;
            }
            .progress-bar {
                background: #475569;
            }
        }
        
        .dark .chart-card {
            background-color: #1e293b;
            border-color: #334155;
        }
        
        .dark .chart-header {
            background: linear-gradient(90deg, #1e293b 0%, #334155 100%);
            border-color: #475569;
        }
        
        .dark .progress-bar {
            background: #475569;
        }
    </style>

    <div class="space-y-6">
        <!-- Refresh Button -->
        <div class="flex justify-end">
            <x-filament::button 
                wire:click="refreshData"
                class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700"
                icon="heroicon-m-arrow-path"
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>Refresh Data</span>
                <span wire:loading>Refreshing...</span>
            </x-filament::button>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="metric-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Total Revenue</p>
                        <p class="text-2xl font-bold">UGX {{ number_format($totalRevenue) }}</p>
                    </div>
                    <div class="text-3xl opacity-80">💰</div>
                </div>
            </div>

            <div class="metric-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Total Orders</p>
                        <p class="text-2xl font-bold">{{ number_format($totalOrders) }}</p>
                    </div>
                    <div class="text-3xl opacity-80">📦</div>
                </div>
            </div>

            <div class="metric-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Pending Orders</p>
                        <p class="text-2xl font-bold">{{ number_format($pendingOrders) }}</p>
                    </div>
                    <div class="text-3xl opacity-80">⏳</div>
                </div>
            </div>

            <div class="metric-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-80">Completed Orders</p>
                        <p class="text-2xl font-bold">{{ number_format($completedOrders) }}</p>
                    </div>
                    <div class="text-3xl opacity-80">✅</div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Revenue Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">📈 Monthly Revenue Trend</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($monthlyRevenue as $month)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-300">{{ $month['month'] }}</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">UGX {{ number_format($month['revenue']) }}</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ $totalRevenue > 0 ? ($month['revenue'] / $totalRevenue) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Status Distribution -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">📊 Order Status Distribution</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($orderStatusDistribution as $status)
                            @php
                                $colors = [
                                    'pending' => 'bg-yellow-500',
                                    'processing' => 'bg-blue-500',
                                    'completed' => 'bg-green-500',
                                    'cancelled' => 'bg-red-500'
                                ];
                                $colorClass = $colors[$status['status']] ?? 'bg-gray-500';
                                $percentage = $totalOrders > 0 ? ($status['count'] / $totalOrders) * 100 : 0;
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-4 h-4 {{ $colorClass }} rounded-full"></div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ $status['status'] }}</span>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $status['count'] }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($percentage, 1) }}%</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Top Products -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">🏆 Top Selling Products</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($topProducts as $index => $product)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-slate-700 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $product['product']['name'] ?? 'Unknown Product' }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $product['total_quantity'] }} units sold</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-green-600">UGX {{ number_format($product['total_revenue']) }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <div class="text-4xl mb-2">📦</div>
                                <p>No sales data available yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">🕒 Recent Orders</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse($recentActivity as $order)
                            <div class="p-3 bg-gray-50 dark:bg-slate-700 rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-medium text-gray-900 dark:text-white">Order #{{ $order['id'] }}</div>
                                    <span class="text-xs px-2 py-1 rounded-full 
                                        {{ $order['status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $order['status'] === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $order['status'] === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                    ">
                                        {{ ucfirst($order['status']) }}
                                    </span>
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-300 mb-1">
                                    {{ count($order['order_items']) }} item(s) • UGX {{ number_format($order['total']) }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($order['created_at'])->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                                <div class="text-4xl mb-2">📋</div>
                                <p>No recent orders</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
