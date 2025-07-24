<x-filament-panels::page>
<div>
    <h1 class="text-3xl font-bold mb-8 text-center text-primary-900 dark:text-primary-100">
        Checkout Page <span class="px-2 text-primary-500 animate-pulse"> {{ $cartCount }} </span> Items in the Cart
    </h1>
    @forelse ($cart as $item)
        {{-- Main cart item container with flex layout --}}
        <div class="transform transition-all duration-300 hover:scale-[1.01] flex gap-8 p-6 rounded-2xl mb-6 bg-gradient-to-br from-primary-800/95 to-primary-900/95 dark:from-gray-800 dark:to-primary-900 shadow-xl">
            {{-- Left side: Product details and controls --}}
            <div class="flex-1 space-y-6">
                {{-- Product image --}}
                <div style="background-color: white !important;" class="w-48 h-48 flex items-start justify-center bg-white rounded-md mb-3 overflow-hidden flex-shrink-0">
                    <img src="{{ asset($item['image'] ?? 'images/default-product.png') }}" alt="{{ $item['name'] ?? 'Product' }}" class="w-auto h-auto object-cover rounded max-w-none min-w-0 min-h-0" style="width: 280px !important; height: auto !important;">
                </div>
                
                {{-- Product info and quantity controls in one row --}}
                <div class="flex justify-between items-start space-x-8">
                    {{-- Product info --}}
                    <div>
                        <h4 class="font-semibold text-lg">{{ $item['name'] ?? 'Unknown Product' }}s</h4>
                        <p class="">Price: UGX {{ $item['price'] ?? 0 }}</p>
                        <p class="">Quantity: {{ $item['quantity'] ?? 0 }}</p>
                    </div>
                    
                    {{-- Quantity control buttons --}}
                    <div class="space-y-3">
                        <div class="flex gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 100)" color="danger" size="sm" icon="heroicon-m-minus" 
                                class="transform transition-all duration-300 hover:scale-105">100</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 100)" color="success" size="sm" icon="heroicon-m-plus"
                                class="transform transition-all duration-300 hover:scale-105">100</x-filament::button>
                        </div>
                        <div class="flex gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 350)" color="danger" size="sm" icon="heroicon-m-minus"
                                class="transform transition-all duration-300 hover:scale-105">350</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 350)" color="success" size="sm" icon="heroicon-m-plus"
                                class="transform transition-all duration-300 hover:scale-105">350</x-filament::button>
                        </div>
                        <div class="flex gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 750)" color="danger" size="sm" icon="heroicon-m-minus"
                                class="transform transition-all duration-300 hover:scale-105">750</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 750)" color="success" size="sm" icon="heroicon-m-plus"
                                class="transform transition-all duration-300 hover:scale-105">750</x-filament::button>
                        </div>
                    </div>
                </div>
                
                {{-- Action buttons --}}
                <div class="flex gap-4 pt-4">
                    <x-filament::button wire:click="removeItem({{ $item['id'] }})" color="danger" size="md"
                        class="transform transition-all duration-300 hover:scale-105">
                        <x-heroicon-m-trash class="w-5 h-5 mr-2" /> Remove Item
                    </x-filament::button>
                    <x-filament::button wire:click="placeOrder({{ $item['id'] }})" color="success" size="md"
                        class="transform transition-all duration-300 hover:scale-105">
                        <x-heroicon-m-shopping-cart class="w-5 h-5 mr-2" /> Place Order
                    </x-filament::button>
                </div>
            </div>
            
            {{-- Right side: Package breakdown --}}
            <div class="w-96 p-6 rounded-xl bg-gradient-to-br from-primary-700/50 to-primary-800/50 backdrop-blur-sm shadow-lg">
                <h2 class="text-xl font-bold text-primary-100 mb-4 flex items-center">
                    <x-heroicon-m-gift class="w-6 h-6 mr-2 text-primary-300" />
                    Selected Package Breakdown
                </h2>
                @if(isset($item['packages']['premium']) && $item['packages']['premium'] > 0)
                    <div class="mb-3 transform transition-all duration-300 hover:translate-x-2">
                        <div class="flex items-center text-primary-100 space-x-2">
                            <x-heroicon-m-star class="w-5 h-5 text-yellow-400" />
                            <span class="text-base">Premium Packages: <span class="font-semibold">{{ $item['packages']['premium'] }}</span> (5% Discount)</span>
                        </div>
                    </div>
                @endif
                @if(isset($item['packages']['standard']) && $item['packages']['standard'] > 0)
                    <div class="mb-3 transform transition-all duration-300 hover:translate-x-2">
                        <div class="flex items-center text-primary-100 space-x-2">
                            <x-heroicon-m-check-badge class="w-5 h-5 text-blue-400" />
                            <span class="text-base">Classic Packages: <span class="font-semibold">{{ $item['packages']['standard'] }}</span> (3% Discount)</span>
                        </div>
                    </div>
                @endif
                @if(isset($item['packages']['starter']) && $item['packages']['starter'] > 0)
                    <div class="mb-3 transform transition-all duration-300 hover:translate-x-2">
                        <div class="flex items-center text-primary-100 space-x-2">
                            <x-heroicon-m-sparkles class="w-5 h-5 text-green-400" />
                            <span class="text-base">Starter Packages: <span class="font-semibold">{{ $item['packages']['starter'] }}</span> (2% Discount)</span>
                        </div>
                    </div>
                @endif
                <div style="margin-top: 5px !important;" class="mt-4">
                    <h2 style="font-weight:bold !important; color: rgb(31, 221, 31) !important;">Pick Delivery Option</h2>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="delivery_option_{{ $item['id'] }}" 
                                   value="delivery" 
                                   wire:click="updateDeliveryOption({{ $item['id'] }}, 'delivery')"
                                   @if(($delivery_options[$item['id']] ?? '') === 'delivery') checked @endif
                                   class="mr-2"> 
                            <span>Standard Delivery (3-5 days) : UGX 3000</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="delivery_option_{{ $item['id'] }}" 
                                   value="express" 
                                   wire:click="updateDeliveryOption({{ $item['id'] }}, 'express')"
                                   @if(($delivery_options[$item['id']] ?? '') === 'express') checked @endif
                                   class="mr-2"> 
                            <span>Express Delivery (1 day) : UGX 5000</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="delivery_option_{{ $item['id'] }}" 
                                   value="pickup" 
                                   wire:click="updateDeliveryOption({{ $item['id'] }}, 'pickup')"
                                   @if(($delivery_options[$item['id']] ?? '') === 'pickup') checked @endif
                                   class="mr-2"> 
                            <span>Pick Up : UGX 0</span>
                        </label>
                    </div>
                </div>

            </div>
        </div>
            
            
    @empty
        <div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
            {{-- Empty State Container --}}
            <div class="w-full max-w-4xl transform transition-all duration-500 hover:scale-[1.02] bg-gradient-to-br from-primary-50 to-white dark:from-gray-900 dark:to-primary-950 rounded-3xl shadow-2xl overflow-hidden">
                {{-- Content Container --}}
                <div class="relative px-8 py-16 flex flex-col items-center">
                    {{-- Background Pattern --}}
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-primary-200 rounded-full animate-pulse"></div>
                        <div class="absolute -left-20 -bottom-20 w-60 h-60 bg-primary-200 rounded-full animate-pulse delay-150"></div>
                    </div>

                    {{-- Content --}}
                    <div class="relative flex flex-col items-center text-center space-y-8 mb-12">
                        {{-- Bags Icon Container with Animation --}}
                        <div class="relative transform transition-all duration-500 hover:scale-110">
                            <x-heroicon-o-shopping-bag class="w-32 h-32 text-primary-200 dark:text-primary-800 animate-float"/>
                            <x-heroicon-o-shopping-bag class="absolute inset-0 w-32 h-32 text-primary-300 dark:text-primary-700 transform translate-x-2 translate-y-2 animate-float-delayed"/>
                        </div>
                        
                        <div class="space-y-4 transform transition-all duration-500">
                            <h2 class="text-4xl font-bold text-primary-900 dark:text-primary-100">Your Cart is Empty</h2>
                            <p class="text-xl text-primary-600 dark:text-primary-400 max-w-lg">Ready to start your bulk shopping journey? Visit our shop to explore amazing deals.</p>
                        </div>
                    </div>

                    {{-- Action Buttons Container --}}
                    <div class="flex justify-center items-center gap-6">
                        {{-- Shopping Button --}}
                        <a href="{{ url('/vendor/product') }}" 
                           class="group relative inline-flex items-center justify-center bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white text-xl font-medium px-10 py-5 rounded-2xl shadow-xl transition-all duration-300 hover:shadow-primary-500/25 hover:shadow-2xl transform hover:scale-105">
                            <div class="flex items-center space-x-4">
                                {{-- Shopping Bags Icon Container --}}
                                <div class="relative">
                                    <x-heroicon-o-shopping-bag class="w-8 h-8 transition-transform duration-300 group-hover:scale-110 animate-bounce"/>
                                    <x-heroicon-o-shopping-bag class="absolute inset-0 w-8 h-8 transform translate-x-1 translate-y-1 opacity-50 transition-transform duration-300 group-hover:translate-x-2 group-hover:translate-y-2"/>
                                </div>
                                <span class="relative inline-block after:content-[''] after:absolute after:w-full after:scale-x-0 after:h-0.5 after:bottom-0 after:left-0 after:bg-white after:origin-bottom-left after:transition-transform after:duration-300 group-hover:after:scale-x-100">
                                    Shop in Bulk
                                </span>
                                <x-heroicon-m-arrow-right class="w-6 h-6 transition-transform duration-300 group-hover:translate-x-2"/>
                            </div>
                        </a>

                        {{-- View Orders Button --}}
                        <a href="{{ url('/vendor/vendor-orders') }}" 
                           class="group relative inline-flex items-center justify-center bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white text-xl font-medium px-10 py-5 rounded-2xl shadow-xl transition-all duration-300 hover:shadow-purple-500/25 hover:shadow-2xl transform hover:scale-105">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <x-heroicon-o-clipboard-document-list class="w-8 h-8 transition-transform duration-300 group-hover:scale-110 animate-pulse"/>
                                    <x-heroicon-o-clipboard-document-list class="absolute inset-0 w-8 h-8 transform translate-x-1 translate-y-1 opacity-50 transition-transform duration-300 group-hover:translate-x-2 group-hover:translate-y-2"/>
                                </div>
                                <span class="relative inline-block after:content-[''] after:absolute after:w-full after:scale-x-0 after:h-0.5 after:bottom-0 after:left-0 after:bg-white after:origin-bottom-left after:transition-transform after:duration-300 group-hover:after:scale-x-100">
                                    View Orders
                                </span>
                                <x-heroicon-m-arrow-right class="w-6 h-6 transition-transform duration-300 group-hover:translate-x-2"/>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endforelse
</div>
</x-filament-panels::page>

