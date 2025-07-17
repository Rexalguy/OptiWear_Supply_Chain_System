@php
    \Illuminate\Pagination\Paginator::useTailwind();
@endphp

<x-filament-panels::page>
    
    <style>
        .order-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #f3f4f6;
            transition: all 0.3s ease;
        }
        
        .order-card:hover {
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        
        .gradient-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1.5rem;
            border-radius: 1rem 1rem 0 0;
            color: white;
        }
        
        @media (prefers-color-scheme: dark) {
            .order-card {
                background-color: #1e293b;
                border-color: #334155;
            }
        }
        
        .dark .order-card {
            background-color: #1e293b;
            border-color: #334155;
        }
    </style>

    <div class="space-y-8">
        @if ($this->orders->count())
            <div class="order-card overflow-hidden">
                <div class="gradient-header">
                    <h2 class="text-2xl font-bold">📋 Order History</h2>
                    <p class="text-white text-opacity-80 mt-1">{{ $this->orders->count() }} orders found</p>
                </div>
                
                <div class="p-6">
                    <div class="grid gap-6">
                        @foreach ($this->orders as $order)
                            <div class="p-6 bg-gray-50 dark:bg-slate-700 rounded-xl border border-gray-200 dark:border-slate-600 transition-all hover:shadow-md">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order #{{ $order->id }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y - H:i') }}</p>
                                    </div>
                                    <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                </div>
                                
                                <div class="space-y-2 mb-4">
                                    @foreach ($order->orderItems as $item)
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-700 dark:text-gray-300">
                                                {{ $item->product->name ?? 'Product' }} ({{ $item->quantity }}x)
                                            </span>
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                UGX {{ number_format($item->unit_price * $item->quantity) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-slate-600">
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        <span class="inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                            {{ ucfirst($order->delivery_method) }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Total</p>
                                        <span class="text-xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                            UGX {{ number_format($order->total) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-slate-700 dark:to-slate-600 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No orders yet</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-md mx-auto">You haven't placed any orders yet. Start shopping to see your order history here.</p>
                <a href="{{ url('/vendor/product') }}">
                    <x-filament::button 
                        class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border-0 shadow-lg transform hover:scale-105 transition-all duration-200"
                        size="lg"
                        icon="heroicon-m-shopping-bag"
                    >
                        Start Shopping
                    </x-filament::button>
                </a>
            </div>
        @endif
        {{ $this->orders->links() }}
    </div>
</x-filament-panels::page>

