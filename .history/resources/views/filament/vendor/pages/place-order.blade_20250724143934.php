<x-filament-panels::page>
<style>
/* Premium animations and effects */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.3); }
    50% { box-shadow: 0 0 30px rgba(59, 130, 246, 0.6); }
}

@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.cart-item {
    background: linear-gradient(135deg, #374151 0%, #4b5563 50%, #6b7280 100%);
    background-size: 200% 200%;
    animation: gradient-shift 8s ease infinite;
    position: relative;
    overflow: hidden;
}

.cart-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
    animation: shimmer 3s infinite;
}

.premium-glow {
    position: relative;
}

.premium-glow::after {
    content: '';
    position: absolute;
    inset: -2px;
    background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899, #f59e0b);
    border-radius: inherit;
    opacity: 0;
    transition: opacity 0.3s;
    z-index: -1;
    filter: blur(10px);
}

.premium-glow:hover::after {
    opacity: 0.4;
}

.float-animation {
    animation: float 4s ease-in-out infinite;
}

.glass-effect {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.price-gradient {
    background: linear-gradient(135deg, #10b981, #059669, #047857);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    background-size: 200% 200%;
    animation: gradient-shift 3s ease infinite;
}

.quantity-display {
    background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 800;
}
</style>
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 p-6">
    <!-- Enhanced premium header -->
    <div class="relative mb-8 text-center">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 via-purple-600/20 to-pink-600/20 rounded-2xl blur-xl"></div>
        <div class="relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl p-8 border border-white/30 dark:border-gray-700/30 shadow-2xl">
            <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-gray-900 via-blue-800 to-purple-900 dark:from-white dark:via-blue-200 dark:to-purple-200 mb-4 leading-tight">
                🛒 Premium Checkout Experience
            </h1>
            <div class="inline-flex items-center space-x-3 bg-gradient-to-r from-orange-500 to-amber-500 text-white px-6 py-3 rounded-full shadow-lg">
                <span class="text-lg font-bold">{{ $cartCount }}</span>
                <span class="text-base">{{ $cartCount === 1 ? 'Item' : 'Items' }} in Cart</span>
                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
            </div>
        </div>
    </div>
    @forelse ($cart as $item)
        {{-- Premium cart item container with enhanced design --}}
        <div class="cart-item relative flex gap-8 p-6 rounded-xl mb-6 shadow-2xl border border-white/20 hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2 premium-glow float-animation" style="border-radius: 1rem !important;">
            <!-- Decorative corner accents -->
            <div class="absolute top-0 left-0 w-16 h-16 bg-gradient-to-br from-blue-500/30 to-purple-500/30 rounded-br-2xl"></div>
            <div class="absolute bottom-0 right-0 w-12 h-12 bg-gradient-to-tl from-purple-500/30 to-pink-500/30 rounded-tl-2xl"></div>
            
            {{-- Enhanced left side: Product details and controls --}}
            <div class="flex-1 relative z-10">
                {{-- Premium product image with enhanced effects --}}
                <div class="relative group mb-6">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-xl blur-lg group-hover:blur-xl transition-all duration-300"></div>
                    <div class="relative w-52 h-52 flex items-center justify-center bg-gradient-to-br from-white via-blue-50 to-purple-50 dark:from-gray-800 dark:via-gray-700 dark:to-gray-800 rounded-xl overflow-hidden border-4 border-white/50 dark:border-gray-600/50 shadow-2xl group-hover:scale-105 transition-all duration-500" style="border-radius: 0.75rem !important;">
                        <!-- Image glow effect -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-pink-500/10"></div>
                        <img src="{{ asset($item['image'] ?? 'images/default-product.png') }}" 
                             alt="{{ $item['name'] ?? 'Product' }}" 
                             class="w-full h-full object-cover transition-all duration-500 group-hover:brightness-110 group-hover:scale-110" 
                             style="border-radius: 0.75rem !important;">
                        <!-- Shimmer overlay -->
                        <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    </div>
                </div>
                
                {{-- Enhanced product info and quantity controls --}}
                <div class="flex justify-between items-start mb-6">
                    {{-- Premium product info --}}
                    <div class="space-y-3">
                        <h4 class="text-2xl font-black text-white mb-2 flex items-center space-x-2">
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-400">{{ $item['name'] ?? 'Unknown Product' }}s</span>
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        </h4>
                        <div class="space-y-2">
                            <p class="text-lg text-white/90 flex items-center space-x-2">
                                <span class="text-gray-300">💰 Price:</span>
                                <span class="price-gradient text-xl font-bold">UGX {{ number_format($item['price'] ?? 0) }}</span>
                            </p>
                            <p class="text-lg text-white/90 flex items-center space-x-2">
                                <span class="text-gray-300">📦 Quantity:</span>
                                <span class="quantity-display text-xl">{{ number_format($item['quantity'] ?? 0) }}</span>
                                <span class="text-sm text-blue-300">pieces</span>
                            </p>
                        </div>
                    </div>
                    
                    {{-- Premium quantity control buttons --}}
                    <div class="space-y-3">
                        <h5 class="text-sm font-bold text-blue-300 mb-2 flex items-center space-x-1">
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
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] ?? $loop->index }}, 750)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before" class="font-semibold">750</x-filament::button>
                        </div>
                    </div>
                </div>
                
                {{-- Premium action buttons --}}
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
            
            {{-- Premium right side: Package breakdown with enhanced design --}}
            <div class="w-80 relative">
                <!-- Glowing background effect -->
                <div class="absolute inset-0 bg-gradient-to-br from-slate-400 via-slate-500 to-slate-600 rounded-xl blur-sm"></div>
                <div class="relative glass-effect p-6 rounded-xl shadow-2xl border border-white/30" style="border-radius: 0.75rem !important; background: linear-gradient(135deg, rgba(148, 163, 184, 0.95), rgba(100, 116, 139, 0.95));">
                    <!-- Decorative header -->
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        <h2 class="font-black text-xl text-white flex items-center space-x-2">
                            <span>📊 Package Breakdown</span>
                        </h2>
                    </div>
                    
                    <!-- Package list with enhanced styling -->
                    <div class="space-y-3 mb-6">
                    @if(isset($item['packages']['premium']) && $item['packages']['premium'] > 0)
                        <div class="bg-gradient-to-r from-purple-500/20 to-pink-500/20 p-3 rounded-lg border border-purple-300/30">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">💎</span>
                                <span class="text-white font-bold">Premium Packages: {{ $item['packages']['premium'] }}</span>
                                <span class="bg-gradient-to-r from-purple-400 to-pink-400 text-white text-xs px-2 py-1 rounded-full">5% Discount</span>
                            </div>
                        </div>
                    @endif
                    @if(isset($item['packages']['standard']) && $item['packages']['standard'] > 0)
                        <div class="bg-gradient-to-r from-blue-500/20 to-cyan-500/20 p-3 rounded-lg border border-blue-300/30">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">⭐</span>
                                <span class="text-white font-bold">Classic Packages: {{ $item['packages']['standard'] }}</span>
                                <span class="bg-gradient-to-r from-blue-400 to-cyan-400 text-white text-xs px-2 py-1 rounded-full">3% Discount</span>
                            </div>
                        </div>
                    @endif
                    @if(isset($item['packages']['starter']) && $item['packages']['starter'] > 0)
                        <div class="bg-gradient-to-r from-green-500/20 to-emerald-500/20 p-3 rounded-lg border border-green-300/30">
                            <div class="flex items-center space-x-2">
                                <span class="text-2xl">🚀</span>
                                <span class="text-white font-bold">Starter Packages: {{ $item['packages']['starter'] }}</span>
                                <span class="bg-gradient-to-r from-green-400 to-emerald-400 text-white text-xs px-2 py-1 rounded-full">2% Discount</span>
                            </div>
                        </div>
                    @endif
                    </div>
                    
                    <!-- Enhanced delivery options -->
                    <div class="border-t border-white/20 pt-4">
                        <div class="flex items-center space-x-2 mb-4">
                            <span class="text-2xl">🚚</span>
                            <h3 class="font-black text-lg text-white">Delivery Options</h3>
                        </div>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 bg-white/10 rounded-lg border border-white/20 hover:bg-white/20 transition-all duration-300 cursor-pointer group">
                                <input type="radio" 
                                       name="delivery_option_{{ $item['id'] ?? $loop->index }}" 
                                       value="delivery" 
                                       wire:click="updateDeliveryOption({{ $item['id'] ?? $loop->index }}, 'delivery')"
                                       @if(($delivery_options[$item['id'] ?? $loop->index] ?? '') === 'delivery') checked @endif
                                       class="mr-3 w-4 h-4 text-blue-500"> 
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-white font-semibold">🚚 Standard Delivery</span>
                                        <span class="text-green-300 font-bold">UGX 3,000</span>
                                    </div>
                                    <span class="text-gray-300 text-sm">3-5 business days</span>
                                </div>
                            </label>
                            <label class="flex items-center p-3 bg-white/10 rounded-lg border border-white/20 hover:bg-white/20 transition-all duration-300 cursor-pointer group">
                                <input type="radio" 
                                       name="delivery_option_{{ $item['id'] ?? $loop->index }}" 
                                       value="express" 
                                       wire:click="updateDeliveryOption({{ $item['id'] ?? $loop->index }}, 'express')"
                                       @if(($delivery_options[$item['id'] ?? $loop->index] ?? '') === 'express') checked @endif
                                       class="mr-3 w-4 h-4 text-blue-500"> 
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-white font-semibold">⚡ Express Delivery</span>
                                        <span class="text-yellow-300 font-bold">UGX 5,000</span>
                                    </div>
                                    <span class="text-gray-300 text-sm">Next day delivery</span>
                                </div>
                            </label>
                            <label class="flex items-center p-3 bg-white/10 rounded-lg border border-white/20 hover:bg-white/20 transition-all duration-300 cursor-pointer group">
                                <input type="radio" 
                                       name="delivery_option_{{ $item['id'] ?? $loop->index }}" 
                                       value="pickup" 
                                       wire:click="updateDeliveryOption({{ $item['id'] ?? $loop->index }}, 'pickup')"
                                       @if(($delivery_options[$item['id'] ?? $loop->index] ?? '') === 'pickup') checked @endif
                                       class="mr-3 w-4 h-4 text-blue-500"> 
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <span class="text-white font-semibold">🏪 Store Pickup</span>
                                        <span class="text-green-400 font-bold">FREE</span>
                                    </div>
                                    <span class="text-gray-300 text-sm">Pick up at our store</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
        </div>
            
            
    @empty
        <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-4">
                    🛒
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
                <p class="text-gray-500 dark:text-gray-400">Add some products to get started</p>
            </div>
    @endforelse
</div>
</x-filament-panels::page>

