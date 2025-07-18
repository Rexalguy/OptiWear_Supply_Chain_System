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
            <div class="flex flex-col items-center md:items-start">
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                    <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : '/images/image.png' }}" alt="{{ $item['name'] }}" class="h-full w-auto object-contain">
                </div>
                <div class="flex justify-between">
                    <div class="flex flex-col items-between justify-between">
                        <div></div>
                        <div>
                            <x-filament::button color="danger" size="x-lg" icon="heroicon-m-plus" icon-position="before">
                                Remve from Cart
                            </x-filament::button
                            <x-filament::button color="Success" size="x-lg" icon="heroicon-m-plus" icon-position="before">
                                Place Order
                            </x-filament::button
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
