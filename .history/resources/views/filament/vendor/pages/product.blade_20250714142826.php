<x-filament::page>
    <div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
            @foreach ($this->products as $product)
                <div class="border rounded-xl shadow-sm p-4">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-40 object-cover rounded-md mb-2">
                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                    <p class="text-sm text-gray-600">Price: UGX {{ number_format($product->price) }}</p>
                    <p class="text-sm text-gray-600">Available: {{ $product->quantity_available }}</p>
                </div>
            @endforeach
        </div>
    </div>
</x-filament::page>
