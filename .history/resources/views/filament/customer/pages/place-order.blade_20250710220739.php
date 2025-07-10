<x-filament-panels::page>
    <div class="p-6 space-y-6">

        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🛍️ Available Products</h2>
<div class="mt-6 text-right">
   <x-filament::button color="primary" tag="a" href="{{ url('/customer/my-orders') }}">
    View cart ({{ $this->cartCount }})
</x-filament::button>
</div>
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="border p-4 rounded-lg shadow bg-white dark:bg-gray-800">
                    <div class="mb-4">
                        <img src="https://via.placeholder.com/150?text=Shirt" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded">
                    </div>                   

                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                    <p class="text-sm ">
                        SKU: {{ $product->sku }}
                    </p>
                    <p class="text-sm ">UGX {{ number_format($product->price) }}</p>
                    <p class="text-sm mt-1 {{ $product->quantity_available > 10 ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $product->quantity_available }} in stock
                    </p>

                    <div class="mt-4">
                        <x-filament::button wire:click="addToCart({{ $product->id }})">
                            Add to Cart
                        </x-filament::button>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-filament-panels::page>