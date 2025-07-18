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
        <div class="grid grid-cols-1 rounded-xl p-4 shadow bg-white dark:bg-gray-900 md:grid-cols-2  items-center justify-between mb-4">
            
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
