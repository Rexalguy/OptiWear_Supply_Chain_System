<x-filament-panels::page>
<div>
    <div class="flex justify-end mb-4">
        <a href="{{ url('/vendor/place-order') }}" title="View Cart">
            <button class="bg-blue-500 text-white px-4 py-2 rounded">
                Cart ({{ $cartCount }}) Items
            </button>
        </a>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach ($products as $product)
        <div class="bg-white p-4 rounded shadow">
            <div class="w-full h-40 bg-gray-100 rounded mb-2 flex items-center justify-center">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="h-full w-auto object-contain">
            </div>
            <h3 class="font-semibold">{{ $product->name }}</h3>
            <p class="text-sm">SKU: {{ $product->sku }}</p>
            <p class="text-sm">Price: UGX {{ number_format($product->price) }}</p>
            
            <select wire:model="bale_sizes.{{ $product->id }}" class="w-full p-2 border rounded my-2" style="background: #119ae9; color: white;">
                <option value="">Select Bale Size</option>
                <option value="100">Starter: 100 pieces</option>
                <option value="350">Classic: 350 pieces</option>
                <option value="750">Premium: 750 pieces</option>
            </select>
            
            <button wire:click="addToCart({{ $product->id }})" class="w-full bg-orange-500 text-white py-2 rounded">
                Add to Cart
            </button>
        </div>
        @endforeach
    </div>
</div>
</x-filament-panels::page>