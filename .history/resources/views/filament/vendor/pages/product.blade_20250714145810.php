<x-filament-panels::page>
    <div>
        <div class="rounded border-gray-200 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
            @foreach ($this->products as $product)
            <div 
                class="rounded-xl shadow-sm p-4 cursor-pointer" 
                wire:click="$dispatch('openProductModal', {id: {{ $product->id }} })
            >
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-auto object-contain">
                </div>
                <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                <p class="text-sm text-gray-600">Price: UGX {{ number_format($product->price) }}</p>
                <p class="text-sm text-gray-600">Available: {{ $product->quantity_available }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <x-filament::modal id="productModal">
        @if ($selectedProduct)
        <div class="p-4">
            <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-4 overflow-hidden">
                <img src="{{ asset('storage/' . $selectedProduct->image) }}" alt="{{ $selectedProduct->name }}" class="h-full w-auto object-contain">
            </div>
            <h3 class="text-lg font-semibold">{{ $selectedProduct->name }}</h3>
            <p class="text-sm text-gray-500">SKU: {{ $selectedProduct->sku }}</p>
            <p class="text-sm text-gray-600">Price: UGX {{ number_format($selectedProduct->price) }}</p>
            <p class="text-sm text-gray-600">Available: {{ $selectedProduct->quantity_available }}</p>
            <p class="text-sm text-gray-600">{{ $selectedProduct->description }}</p>
        </div>
        @endif
    </x-filament::modal>
</x-filament-panels::page>
