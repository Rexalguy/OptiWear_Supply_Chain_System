<x-filament-panels::page>
    <script>
        const cartCount = @json($cartCount);
        const cart = @json($cart);
        console.log('Cart Count:', cartCount);
        console.log('Cart:', cart);
    </script>
<div>
    <h1 class="text-2xl font-bold mb-4 flex justify-center">
        Checkout Page <span style="color: orange" class="px-2"> {{ $cartCount }} </span> Items in the Cart
    </h1>
    @forelse ($cart as $item)
        <div class="grid grid-cols-1 md:grid-cols-2  items-center justify-between p-4 ">
            <div class="rounded-xl  shadow bg-white dark:bg-gray-800">
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                    <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : '/images/image.png' }}" alt="{{ $item['name'] }}" class="h-full w-auto object-contain">
                </div>
                <div class="flex gap-4 items-center align-top mt-0">
                    <div style="margin-top: -30px;" class="w-full text-start md:text-left">
                        <h4 class="font-semibold">{{ $item['name'] }}</h4>
                        <p>Price: {{ $item['price'] }}</p>
                        <p>Quantity: {{ $item['quantity'] }}</p>
                    </div>
                    <div class="flex flex-col gap-4 items-center align-top mt-0">
                        <div class="flex space-x-2 gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 100)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">100</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 100)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">100</x-filament::button>
                        </div>
                        <div class="flex space-x-2 gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 350)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">350</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 350)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">350</x-filament::button>
                        </div>
                        <div class="flex space-x-2 gap-3">
                            <x-filament::button wire:click="reduceQuantity({{ $item['id'] }}, 750)" color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">750</x-filament::button>
                            <x-filament::button wire:click="increaseQuantity({{ $item['id'] }}, 750)" color="success" size="xs" icon="heroicon-m-plus" icon-position="before">750</x-filament::button>
                        </div>
                    </div>
                    <div class="flex flex-col gap-4 items-center align-top mt-0">
                        <div class="flex space-x-2 gap-3">
                            <x-filament::button color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">Remove Item</x-filament::button>
                            <x-filament::button color="success" size="xs" icon="heroicon-m-plus" icon-position="before">Place Order</x-filament::button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end mt-4">
                <h1>Additional Information</h1>
            </div>
        </div>
    @empty
        <div class="text-center text-gray-500 my-5 py-5">
            <p>Your cart is empty. Please add some products to proceed with checkout.</p>
        </div>
    @endforelse
</div>
</x-filament-panels::page>
