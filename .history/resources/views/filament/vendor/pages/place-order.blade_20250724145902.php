<x-filament-panels::page>
    <style>
        .small-toast {
            font-size: 14px !important;
            padding: 8px 12px !important;
            min-height: auto !important;
        }
        .small-toast .swal2-icon {
            width: 20px !important;
            height: 20px !important;
            margin: 0 8px 0 0 !important;
        }
        .small-toast .swal2-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        .toast-success .swal2-timer-progress-bar {
            height: 3px !important;
            background: #10b981 !important;
        }
        .toast-warning .swal2-timer-progress-bar {
            height: 3px !important;
            background: #f59e0b !important;
        }
        .toast-error .swal2-timer-progress-bar,
        .toast-danger .swal2-timer-progress-bar {
            height: 3px !important;
            background: #ef4444 !important;
        }
        .swal2-popup.swal2-toast {
            transform: none !important;
        }
    </style>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <!-- Clean header -->
    <div class="mb-8 text-center">
        <div class="bg-gradient-to-br from-white via-blue-50 to-purple-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-xl p-6 shadow-lg" style="border-radius: 1rem !important;">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                🛒 Shopping Cart
            </h1>
            <div class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white px-4 py-2 rounded-lg">
                <span class="text-lg font-semibold">{{ $cartCount }}</span>
                <span>{{ $cartCount === 1 ? 'Item' : 'Items' }} in Cart</span>
            </div>
        </div>
    </div>
    @forelse ($cart as $item)
        {{-- Clean cart item container --}}
        <div class="relative flex gap-8 p-6 rounded-xl mb-6 shadow-lg bg-white dark:bg-gray-800 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" style="border-radius: 1rem !important;">
            <!-- Subtle gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 to-purple-50/30 dark:from-gray-800/50 dark:to-gray-900/50 rounded-xl" style="border-radius: 1rem !important;"></div>
            
            {{-- Left side: Product details and controls --}}
            <div class="flex-1 relative z-10">
                {{-- Product image with clean styling --}}
                <div class="relative group mb-6">
                    <div class="relative w-52 h-52 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl overflow-hidden shadow-lg group-hover:scale-105 transition-all duration-300" style="border-radius: 0.75rem !important;">
                        <div class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl" style="border-radius: 0.75rem !important;"></div>
                        <img src="{{ asset($item['image'] ?? 'images/default-product.png') }}" 
                             alt="{{ $item['name'] ?? 'Product' }}" 
                             class="relative z-10 w-full h-full object-cover transition-all duration-300 group-hover:brightness-110" 
                             style="border-radius: 0.75rem !important;">
                        <!-- Hover effect overlay -->
                        <div class="absolute inset-0 bg-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl" style="border-radius: 0.75rem !important;"></div>
                    </div>
                </div>
                
                {{-- Enhanced product info and quantity controls --}}
                <div class="flex justify-between items-start mb-6">
                    {{-- Clean product info --}}
                    <div class="space-y-3">
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mb-2 flex items-center space-x-2">
                            <span>{{ $item['name'] ?? 'Unknown Product' }}s</span>
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        </h4>
                        <div class="space-y-2">
                            <p class="text-lg text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                                <span class="text-gray-500 dark:text-gray-400">💰 Price:</span>
                                <span class="text-green-600 dark:text-green-400 text-xl font-bold">UGX {{ number_format($item['price'] ?? 0) }}</span>
                            </p>
                            <p class="text-lg text-gray-700 dark:text-gray-300 flex items-center space-x-2">
                                <span class="text-gray-500 dark:text-gray-400">📦 Quantity:</span>
                                <span class="text-xl font-semibold">{{ number_format($item['quantity'] ?? 0) }}</span>
                                <span class="text-sm text-blue-600 dark:text-blue-400">pieces</span>
                            </p>
                        </div>
                    </div>
                    
                    {{-- Clean quantity control buttons --}}
                    <div class="space-y-3">
                        <h5 class="text-sm font-bold text-blue-600 dark:text-blue-400 mb-2 flex items-center space-x-1">
                            <span>⚡ Quick Actions</span>
                        </h5>
                        <div class="grid grid-cols-2 gap-2">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] ?? $loop->index }}, 100)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before" class="font-semibold">100</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] ?? $loop->index }}, 100)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before" class="font-semibold">100</x-filament::button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] ?? $loop->index }}, 350)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before" class="font-semibold">350</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] ?? $loop->index }}, 350)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before" class="font-semibold">350</x-filament::button>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] ?? $loop->index }}, 750)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before" class="font-semibold">750</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] ?? $loop->index }}, 750)" color="success" size="xs" icon-heroicon-m-plus" icon-position="before" class="font-semibold">750</x-filament::button>
                        </div>
                    </div>
                </div>
                
                {{-- Clean action buttons --}}
                <div class="flex gap-4 pt-4">
                    <x-filament::button 
                        wire:click="removeItem({{ $item['id'] ?? $loop->index }})" 
                        color="danger" 
                        size="sm" 
                        icon="heroicon-m-trash" 
                        icon-position="before"
                        class="flex-1 font-bold py-3 bg-gradient-to-r from-red-500 to-pink-500 hover:from-red-600 hover:to-pink-600 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                    >
                        🗑️ Remove Item
                    </x-filament::button>
                    <x-filament::button 
                        wire:click="placeOrder({{ $item['id'] ?? $loop->index }})" 
                        color="success" 
                        size="sm" 
                        icon="heroicon-m-check-circle" 
                        icon-position="before"
                        class="flex-1 font-bold py-3 bg-gradient-to-r from-green-500 to-emerald-500 hover:from-green-600 hover:to-emerald-600 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
                    >
                        ✅ Place Order
                    </x-filament::button>
                </div>
            </div>
            
            {{-- Clean right side: Package breakdown --}}
            <div class="w-80 relative">
                <div class="relative bg-gradient-to-br from-white to-blue-50/50 dark:from-gray-800/80 dark:to-gray-700/80 p-6 rounded-xl shadow-xl" style="border-radius: 0.75rem !important;">
                    <!-- Clean header -->
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center space-x-2">
                            <span>📊 Package Breakdown</span>
                        </h2>
                    </div>
                    
                    <!-- Package list with enhanced styling -->
                    <div class="space-y-3 mb-6">
                    @if(isset($item['packages']['premium']) && $item['packages']['premium'] > 0)
                        <div class="bg-gradient-to-r from-purple-500/20 to-pink-500/20 p-3 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">💎</span>
                                <span class="text-gray-900 dark:text-white font-bold">Premium Packages: {{ $item['packages']['premium'] }}</span>
                                <span class="bg-gradient-to-r from-purple-400 to-pink-400 text-white text-xs px-2 py-1 rounded-full">5% Discount</span>
                            </div>
                        </div>
                    @endif
                    @if(isset($item['packages']['standard']) && $item['packages']['standard'] > 0)
                        <div class="bg-gradient-to-r from-blue-500/20 to-cyan-500/20 p-3 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">⭐</span>
                                <span class="text-gray-900 dark:text-white font-bold">Classic Packages: {{ $item['packages']['standard'] }}</span>
                                <span class="bg-gradient-to-r from-blue-400 to-cyan-400 text-white text-xs px-2 py-1 rounded-full">3% Discount</span>
                            </div>
                        </div>
                    @endif
                    @if(isset($item['packages']['starter']) && $item['packages']['starter'] > 0)
                        <div class="bg-gradient-to-r from-green-500/20 to-emerald-500/20 p-3 rounded-lg">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">🚀</span>
                                <span class="text-gray-900 dark:text-white font-bold">Starter Packages: {{ $item['packages']['starter'] }}</span>
                                <span class="bg-gradient-to-r from-green-400 to-emerald-400 text-white text-xs px-2 py-1 rounded-full">2% Discount</span>
                            </div>
                        </div>
                    @endif
                    </div>
                    
                    <!-- Enhanced delivery options -->
                    <div class="pt-4">
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="text-2xl">🚚</span>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">Delivery Options</h3>
                        </div>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 bg-white/50 dark:bg-gray-700/50 rounded-lg hover:bg-white/70 dark:hover:bg-gray-700/70 transition-all duration-300 cursor-pointer group">
                                <input type="radio" 
                                       name="delivery_option_{{ $item['id'] ?? $loop->index }}" 
                                       value="delivery" 
                                       wire:click="updateDeliveryOption({{ $item['id'] ?? $loop->index }}, 'delivery')"
                                       @if(($delivery_options[$item['id'] ?? $loop->index] ?? '') === 'delivery') checked @endif
                                       class="mr-3 w-4 h-4 text-blue-500"> 
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-900 dark:text-white font-semibold">🚚 Standard Delivery</span>
                                        <span class="text-green-600 dark:text-green-400 font-bold">UGX 3,000</span>
                                    </div>
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">3-5 business days</span>
                                </div>
                            </label>
                            <label class="flex items-center p-3 bg-white/50 dark:bg-gray-700/50 rounded-lg hover:bg-white/70 dark:hover:bg-gray-700/70 transition-all duration-300 cursor-pointer group">
                                <input type="radio" 
                                       name="delivery_option_{{ $item['id'] ?? $loop->index }}" 
                                       value="express" 
                                       wire:click="updateDeliveryOption({{ $item['id'] ?? $loop->index }}, 'express')"
                                       @if(($delivery_options[$item['id'] ?? $loop->index] ?? '') === 'express') checked @endif
                                       class="mr-3 w-4 h-4 text-blue-500"> 
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-900 dark:text-white font-semibold">⚡ Express Delivery</span>
                                        <span class="text-yellow-600 dark:text-yellow-400 font-bold">UGX 5,000</span>
                                    </div>
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">Next day delivery</span>
                                </div>
                            </label>
                            <label class="flex items-center p-3 bg-white/50 dark:bg-gray-700/50 rounded-lg hover:bg-white/70 dark:hover:bg-gray-700/70 transition-all duration-300 cursor-pointer group">
                                <input type="radio" 
                                       name="delivery_option_{{ $item['id'] ?? $loop->index }}" 
                                       value="pickup" 
                                       wire:click="updateDeliveryOption({{ $item['id'] ?? $loop->index }}, 'pickup')"
                                       @if(($delivery_options[$item['id'] ?? $loop->index] ?? '') === 'pickup') checked @endif
                                       class="mr-3 w-4 h-4 text-blue-500"> 
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-900 dark:text-white font-semibold">🏪 Store Pickup</span>
                                        <span class="text-green-600 dark:text-green-400 font-bold">FREE</span>
                                    </div>
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">Pick up at our store</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
    @empty
        {{-- Clean empty cart design --}}
        <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-xl shadow-lg" style="border-radius: 1rem !important;">
            <!-- Subtle gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 to-purple-50/30 dark:from-gray-800/50 dark:to-gray-900/50 rounded-xl" style="border-radius: 1rem !important;"></div>
            
            <!-- Simple cart icon -->
            <div class="relative z-10 mx-auto w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-500 rounded-xl flex items-center justify-center mb-8 shadow-lg">
                <span class="text-4xl">🛒</span>
            </div>
            
            <!-- Clean empty message -->
            <div class="relative z-10">
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                    Your Cart is Empty
                </h3>
                <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                    Ready to start shopping? Add some products to get started!
                </p>
                
                <!-- Simple call to action -->
                <div class="inline-flex items-center space-x-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white px-8 py-4 rounded-lg shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                    <span class="text-lg font-bold">Start Shopping</span>
                </div>
            </div>
        </div>
    @endforelse
</div>
</x-filament-panels::page>

