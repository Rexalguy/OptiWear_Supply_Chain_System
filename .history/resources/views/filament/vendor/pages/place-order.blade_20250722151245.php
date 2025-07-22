<x-filament-panels::page>
<div>
    <h1 class="text-2xl font-bold mb-4 flex justify-center">
        Checkout Page <span style="color: orange" class="px-2"> {{ $cartCount }} </span> Items in the Cart
    </h1>
    @forelse ($cart as $item)
        {{-- Main cart item container with flex layout --}}
        <div style="color: white !important; background-color: #4b5563; !important;" class="flex gap-6 p-4  rounded-lg mb-4 shadow-sm">
            {{-- Left side: Product details and controls --}}
            <div class="flex-1">
                {{-- Product image --}}
                <div style="background-color: white !important;" class="w-48 h-48 flex items-start justify-center bg-white rounded-md mb-3 overflow-hidden flex-shrink-0">
                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-auto h-auto object-cover rounded max-w-none min-w-0 min-h-0" style="width: 280px !important; height: auto !important;">
                </div>
                
                {{-- Product info and quantity controls in one row --}}
                <div class="flex justify-between items-start mb-4">
                    {{-- Product info --}}
                    <div>
                        <h4 class="font-semibold text-lg">{{ $item['name'] }}</h4>
                        <p class="">Price: UGX {{ $item['price'] }}</p>
                        <p class="">Quantity: {{ $item['quantity'] }}</p>
                    </div>
                    
                    {{-- Quantity control buttons --}}
                    <div class="space-y-2 mt-3">
                        <div class="flex gap-2 space-x-2">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 100)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">100</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 100)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">100</x-filament::button>
                        </div>
                        <div class="flex gap-2 space-x-2">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 350)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">350</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 350)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">350</x-filament::button>
                        </div>
                        <div class="flex gap-2 space-x-2">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 750)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">750</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 750)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">750</x-filament::button>
                        </div>
                    </div>
                </div>
                
                {{-- Action buttons --}}
                <div class="flex gap-3">
                    <x-filament::button wire:click="removeItem({{ $item['id'] }})" padding="sm" color="danger" size="sm">Remove Item</x-filament::button>
                    <x-filament::button wire:click="placeOrder({{ $item['id'] }})" padding="sm" color="success" size="sm">Place Order</x-filament::button>
                </div>
            </div>
            
            {{-- Right side: Package breakdown --}}
            <div class="w-80 p-4 rounded-lg bg-slate-400">
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
                    <input type="radio" name="delivery_option" value="standard"> Standard Delivery (3-5 days) : UGX 3000 <br>
                    <input type="radio" name="delivery_option" value="express"> Express Delivery (1 day) : UGX 5000 <br>
                    <input type="radio" name="delivery_option" value="pickup"> Pick Up : UGX 0 <br>
                </div>

            </div>
        </div>
            
            
    @empty
        <div class="text-center text-gray-500 my-5 py-5">
            <p>Your cart is empty. Please add some products to proceed with checkout.</p>
        </div>
    @endforelse
</div>
</x-filament-panels::page>
