<x-filament-panels::page>
<div>
    <h1 class="text-2xl font-bold mb-4 flex justify-center">
        Checkout Page <span style="color: orange" class="px-2"> {{ $cartCount }} </span> Items in the Cart
    </h1>
    @forelse ($cart as $item)
            
            <div>
                left side
                {{-- starting right side --}}
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
