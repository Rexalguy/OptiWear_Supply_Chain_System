<x-filament-panels::page>
<div>
    <h1 class="text-2xl font-bold mb-4 flex justify-center">
        Checkout Page <span style="color: orange" class="px-2"> {{ $cartCount }} </span> Items in the Cart
    </h1>
    @forelse ($cart as $item)
            <div class="flex flex-col-1  ">
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="h-full w-auto object-contain">
                </div>
                <div class="flex gap-4 items-center align-top mt-0">
                    <div style="margin-top: -30px;" class="w-full text-start md:text-left">
                        <h4 class="font-semibold">{{ $item['name'] }}</h4>
                        <p>Price: UGX {{ $item['price'] }}</p>
                        <p>Quantity: {{ $item['quantity'] }}</p>
                    </div>
                    <div class="flex flex-col gap-4 items-center align-top mt-0">
                        <div class="flex space-x-5 gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 100)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">100</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 100)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">100</x-filament::button>
                        </div>
                        <div class="flex space-x-5 gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 350)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">350</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 350)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">350</x-filament::button>
                        </div>
                        <div class="flex space-x-5 gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 750)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">750</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 750)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">750</x-filament::button>
                        </div>
                    </div>
            </div>
            <div>
                <div class="flex gap-4 items-center align-top mt-0">
                            <x-filament::button wire:click="removeItem({{ $item['id'] }})" padding="sm" color="danger" size="sm" >Remove Item</x-filament::button>
                            <x-filament::button wire:click="placeOrder({{ $item['id'] }})" padding="sm" color="success" size="sm">Place Order</x-filament::button>

                    </div>
            </div>
            <div>

                <div>
                        <h2>Package Breakdown</h2>
                @if(isset($item['packages']['premium']) && $item['packages']['premium'] > 0)
                <div class="mt-2">
                    <p class="text-sm text-gray-600">Premium Packages: {{ $item['packages']['premium'] }}</p>
                </div>
                @endif
                @if(isset($item['packages']['standard']) && $item['packages']['standard'] > 0)
                    <div class="mt-2">
                        <p class="text-sm text-gray-600">Standard Packages: {{ $item['packages']['standard'] }}</p>
                    </div>
                @endif
                @if(isset($item['packages']['starter']) && $item['packages']['starter'] > 0)
                    <div class="mt-2">
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
