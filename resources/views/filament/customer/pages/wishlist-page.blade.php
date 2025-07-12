<x-filament-panels::page>
    <div class="p-6 space-y-6">
                @if ($potentialTokens > 0)
            <div class="text-sm text-green-600 dark:text-green-400 mb-4">
                🎁 You will earn <strong>{{ $potentialTokens }}</strong> token{{ $potentialTokens > 1 ? 's' : '' }} for this order!
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($wishlistItems as $item)
                <div class="border p-4 rounded-lg shadow bg-white dark:bg-gray-800 flex flex-col justify-between">
                    <div>
                        <img src="{{ $item->product->image ?? '/images/image.png' }}" 
                             alt="{{ $item->product->name }}" 
                             class="w-full h-48 object-cover rounded mb-4">

                        <h3 class="text-lg font-semibold">{{ $item->product->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            UGX {{ number_format($item->product->price) }}
                        </p>
                    </div>

                    <div class="mt-4 flex justify-between">
                        @if (isset($cart[$item->product->id]))
                            <x-filament::button disabled size="sm" color="gray">
                                Already in Cart
                            </x-filament::button>
                        @else
                            <x-filament::button 
                                color="primary" 
                                wire:click="addToCart({{ $item->product->id }})"
                                size="sm">
                                Add to Cart
                            </x-filament::button>
                        @endif

                        <x-filament::button 
                            color="danger" 
                            size="sm"
                            wire:click="removeFromWishlist({{ $item->id }})">
                            Remove
                        </x-filament::button>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 dark:text-gray-400 col-span-full text-center">
                    Your wishlist is empty.
                </p>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>