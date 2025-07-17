<x-filament-panels::page>
    <style>
        .cart-card, .order-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #f3f4f6;
            transition: all 0.3s ease;
        }
        
        .cart-card:hover, .order-card:hover {
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
            .cart-card, .order-card {
                background-color: #1e293b;
                border-color: #334155;
            }
        }
        
        .dark .cart-card, .dark .order-card {
            background-color: #1e293b;
            border-color: #334155;
        }
    </style>

    <div class="space-y-8">
        <!-- Loading State -->
        <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
            <div class="bg-white dark:bg-slate-800 rounded-lg p-6 flex items-center space-x-3">
                <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-600 dark:text-gray-300">Processing...</span>
            </div>
        </div>

        <!-- Cart Section -->
        @if (!empty($cart))
            <div class="cart-card overflow-hidden">
                <div class="gradient-header">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold">🛒 Your Cart</h2>
                        <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm font-semibold">
                            {{ $cartCount }} items
                        </span>
                    </div>
                </div>
                
                <div class="p-6 space-y-4">
                    @foreach ($cart as $item)
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-slate-700 rounded-xl">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-white">{{ $item['name'] ?? 'Unknown Product' }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Quantity: {{ $item['quantity'] ?? 0 }} pieces</p>
                                <p class="text-lg font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                    UGX {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0)) }}
                                </p>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <input 
                                    type="number" 
                                    wire:change="updateQuantity({{ $item['id'] ?? 0 }}, $event.target.value)"
                                    value="{{ $item['quantity'] ?? 0 }}"
                                    min="100"
                                    class="w-20 px-2 py-1 border border-gray-300 dark:border-slate-600 rounded-lg text-center focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"
                                >
                                <x-filament::button 
                                    wire:click="removeFromCart({{ $item['id'] ?? 0 }})"
                                    color="danger"
                                    size="sm"
                                    icon="heroicon-m-trash"
                                >
                                </x-filament::button>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- Cart Total -->
                    <div class="border-t pt-4 mt-4">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xl font-semibold text-gray-900 dark:text-white">Total:</span>
                            <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                UGX {{ number_format($this->getTotalAmount()) }}
                            </span>
                        </div>
                        
                        <!-- Order Form -->
                        <div class="space-y-4">
                            {{ $this->form }}
                            
                            <x-filament::button 
                                wire:click="placeOrder"
                                class="w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 border-0 shadow-lg transform hover:scale-105 transition-all duration-200"
                                size="lg"
                                icon="heroicon-m-check-circle"
                                wire:loading.attr="disabled"
                            >
                                <span wire:loading.remove>Place Order</span>
                                <span wire:loading>Processing...</span>
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 dark:bg-slate-700 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 4M7 13L5.5 7M7 13l4.5 4.5M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">Add some products to your cart to get started</p>
                <a href="{{ url('/vendor/product') }}">
                    <x-filament::button 
                        class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border-0 shadow-lg"
                        icon="heroicon-m-shopping-bag"
                    >
                        Continue Shopping
                    </x-filament::button>
                </a>
            </div>
        @endif

        <!-- Order History Section - Lazy Load -->
        <div class="order-card overflow-hidden">
            <div class="gradient-header">
                <div class="flex items-center justify-between">
                    <h2 class="text-2xl font-bold">📋 Order History</h2>
                    @if ($orders->isEmpty())
                        <x-filament::button 
                            wire:click="loadOrderHistory"
                            size="sm"
                            color="secondary"
                            icon="heroicon-m-arrow-path"
                        >
                            Load Orders
                        </x-filament::button>
                    @endif
                </div>
            </div>
            
            <div class="p-6">
                @if ($orders->isNotEmpty())
                    <div class="grid gap-4">
                        @foreach ($orders as $order)
                            <div class="p-4 bg-gray-50 dark:bg-slate-700 rounded-xl border border-gray-200 dark:border-slate-600">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Order #{{ $order->id }}</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-slate-600">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $order->items_count ?? 0 }} item(s) • {{ ucfirst($order->delivery_method) }}
                                    </span>
                                    <span class="text-lg font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">
                                        UGX {{ number_format($order->total) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                        <div class="text-4xl mb-2">📋</div>
                        <p>Click "Load Orders" to view your order history</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
