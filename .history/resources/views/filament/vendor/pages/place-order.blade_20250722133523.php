<x-filament-panels::page>
<div>
    <h1 class="text-2xl font-bold mb-4 flex justify-center">
        Checkout Page <span style="color: orange" class="px-2"> {{ $cartCount }} </span> Items in the Cart
    </h1>
    @forelse ($cart as $item)
        {{-- Main cart item container with flex layout --}}
        <div class="flex gap-6 p-4  bg-gray-200 rounded-lg mb-4 shadow-sm">
            {{-- Left side: Product details and controls --}}
            <div class="flex-1">
                {{-- Product image --}}
                <div class="w-48 h-48 flex items-center justify-cente rounded-md mb-3 overflow-hidden flex-shrink-0">
                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-48 h-48 object-cover rounded max-w-none min-w-0 min-h-0" style="width: 192px !important; height: 192px !important;">
                </div>
                
                {{-- Product info --}}
                <div class="mb-4">
                    <h4 class="font-semibold text-lg">{{ $item['name'] }}</h4>
                    <p class="text-gray-600">Price: UGX {{ $item['price'] }}</p>
                    <p class="text-gray-600">Quantity: {{ $item['quantity'] }}</p>
                </div>
                
                {{-- Quantity control buttons --}}
                <div class="space-y-2 mb-4">
                    <div class="flex space-x-2">
                        <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 100)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">100</x-filament::button>
                        <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 100)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">100</x-filament::button>
                    </div>
                    <div class="flex space-x-2">
                        <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 350)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">350</x-filament::button>
                        <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 350)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">350</x-filament::button>
                    </div>
                    <div class="flex space-x-2">
                        <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 750)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">750</x-filament::button>
                        <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 750)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">750</x-filament::button>
                    </div>
                </div>
                
                {{-- Action buttons --}}
                <div class="flex gap-3">
                    <x-filament::button wire:click="removeItem({{ $item['id'] }})" padding="sm" color="danger" size="sm">Remove Item</x-filament::button>
                    <x-filament::button wire:click="placeOrder({{ $item['id'] }})" padding="sm" color="success" size="sm">Place Order</x-filament::button>
                </div>
            </div>
            
            {{-- Right side: Package breakdown --}}
            <div class="w-80 bg-gray-50 p-4 rounded-lg">
                <h2 class="font-semibold text-lg mb-3">Package Breakdown</h2>
                @if(isset($item['packages']['premium']) && $item['packages']['premium'] > 0)
                    <div class="mb-2">
                        <p class="text-sm text-gray-600">Premium Packages: {{ $item['packages']['premium'] }}</p>
                    </div>
                @endif
                @if(isset($item['packages']['standard']) && $item['packages']['standard'] > 0)
                    <div class="mb-2">
                        <p class="text-sm text-gray-600">Standard Packages: {{ $item['packages']['standard'] }}</p>
                    </div>
                @endif
                @if(isset($item['packages']['starter']) && $item['packages']['starter'] > 0)
                    <div class="mb-2">
                        <p class="text-sm text-gray-600">Starter Packages: {{ $item['packages']['starter'] }}</p>
                    </div>
                @endif
            </div>
        </div>
            
            
    @empty
        <div class="text-center text-gray-500 my-5 py-5">
            <p>Your cart is empty. Please add some products to proceed with checkout.</p>
        </div>
    @endforelse
</div>
</x-filament-panels::page>
