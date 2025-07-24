<x-filament-panels::page>
<div>
    <h1 class="text-2xl font-bold mb-4 flex justify-center">
        Checkout Page <span style="color: orange" class="px-2"> {{ $cartCount }} </span> Items in the Cart
    </h1>
    @forelse ($cart as $item)
        {{-- Main cart item container with flex layout --}}
        <div style="color: white !important; background-color: #4b5563; border-radius: 1rem !important;" class="flex gap-6 p-4 rounded-xl mb-4 shadow-sm">
            {{-- Left side: Product details and controls --}}
            <div class="flex-1">
                {{-- Product image --}}
                <div style="background-color: white !important; border-radius: 1.5rem !important;" class="w-48 h-48 flex items-start justify-center bg-white rounded-[1.5rem] mb-3 overflow-hidden flex-shrink-0">
                    <img src="{{ asset($item['image'] ?? 'images/default-product.png') }}" alt="{{ $item['name'] ?? 'Product' }}" class="w-auto h-auto object-cover rounded-[1.5rem] max-w-none min-w-0 min-h-0" style="width: 280px !important; height: auto !important; border-radius: 1.5rem !important;">
                </div>
                
                {{-- Product info and quantity controls in one row --}}
                <div class="flex justify-between items-start mb-4">
                    {{-- Product info --}}
                    <div>
                        <h4 class="font-semibold text-lg">{{ $item['name'] ?? 'Unknown Product' }}s</h4>
                        <p class="">Price: UGX {{ $item['price'] ?? 0 }}</p>
                        <p class="">Quantity: {{ $item['quantity'] ?? 0 }}</p>
                    </div>
                    
                    {{-- Quantity control buttons --}}
                    <div class="space-y-2 mt-3">
                        <div class="flex gap-2 space-x-2">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] ?? $loop->index }}, 100)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">100</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] ?? $loop->index }}, 100)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">100</x-filament::button>
                        </div>
                        <div class="flex gap-2 space-x-2">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] ?? $loop->index }}, 350)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">350</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] ?? $loop->index }}, 350)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">350</x-filament::button>
                        </div>
                        <div class="flex gap-2 space-x-2">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] ?? $loop->index }}, 750)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">750</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] ?? $loop->index }}, 750)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">750</x-filament::button>
                        </div>
                    </div>
                </div>
                
                {{-- Action buttons --}}
                <div class="flex gap-3">
                    <x-filament::button wire:click="removeItem({{ $item['id'] ?? $loop->index }})" padding="sm" color="danger" size="sm">Remove Item</x-filament::button>
                    <x-filament::button wire:click="placeOrder({{ $item['id'] ?? $loop->index }})" padding="sm" color="success" size="sm">Place Order</x-filament::button>
                </div>
            </div>
            
            {{-- Right side: Package breakdown --}}
            <div class="w-80 p-4 rounded-[1.5rem] bg-slate-400" style="border-radius: 1.5rem !important;">
                <h2 style="font-weight:bold !important; color: rgb(31, 221, 31) !important;" class="font-semibold text-lg mb-3">Selected Package Breakdown</h2>
                @if(isset($item['packages']['premium']) && $item['packages']['premium'] > 0)
                    <div class="mb-2">
                        <li class="text-sm">Premium Packages: {{ $item['packages']['premium'] }} (5% Discount)</li>
                    </div>
                @endif
                @if(isset($item['packages']['standard']) && $item['packages']['standard'] > 0)
                    <div class="mb-2">
                        <li class="text-sm">Classic Packages: {{ $item['packages']['standard'] }} (3% Discount)</li>
                    </div>
                @endif
                @if(isset($item['packages']['starter']) && $item['packages']['starter'] > 0)
                    <div class="mb-2">
                        <li class="text-sm">Starter Packages: {{ $item['packages']['starter'] }} (2% Discount)</li>
                    </div>
                @endif
                <div style="margin-top: 5px !important;" class="mt-4">
                    <h2 style="font-weight:bold !important; color: rgb(31, 221, 31) !important;">Pick Delivery Option</h2>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="delivery_option_{{ $item['id'] ?? $loop->index }}" 
                                   value="delivery" 
                                   wire:click="updateDeliveryOption({{ $item['id'] ?? $loop->index }}, 'delivery')"
                                   @if(($delivery_options[$item['id'] ?? $loop->index] ?? '') === 'delivery') checked @endif
                                   class="mr-2"> 
                            <span>Standard Delivery (3-5 days) : UGX 3000</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="delivery_option_{{ $item['id'] ?? $loop->index }}" 
                                   value="express" 
                                   wire:click="updateDeliveryOption({{ $item['id'] ?? $loop->index }}, 'express')"
                                   @if(($delivery_options[$item['id'] ?? $loop->index] ?? '') === 'express') checked @endif
                                   class="mr-2"> 
                            <span>Express Delivery (1 day) : UGX 5000</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="delivery_option_{{ $item['id'] ?? $loop->index }}" 
                                   value="pickup" 
                                   wire:click="updateDeliveryOption({{ $item['id'] ?? $loop->index }}, 'pickup')"
                                   @if(($delivery_options[$item['id'] ?? $loop->index] ?? '') === 'pickup') checked @endif
                                   class="mr-2"> 
                            <span>Pick Up : UGX 0</span>
                        </label>
                    </div>
                </div>

            </div>
        </div>
            
            
    @empty
        <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-[1rem] flex items-center justify-center mb-4">
                    🛒
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
                <p class="text-gray-500 dark:text-gray-400">Add some products to get started</p>
            </div>
    @endforelse
</div>
</x-filament-panels::page>

