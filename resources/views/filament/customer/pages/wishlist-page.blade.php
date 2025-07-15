<x-filament-panels::page>
    <div class="p-6 space-y-6">
         <div class="mt-6 text-right">
            @if ($potentialTokens > 0)
                <div class="text-sm text-green-600 dark:text-green-400 text-right mb-2">
                    🎁 You will earn <strong>{{ $potentialTokens }}</strong> token{{ $potentialTokens > 1 ? 's' : '' }} for this order!
                </div>
            @else
                <div class="text-sm text-green-600 dark:text-green-400 text-right mb-2">
                    Make a purchase above <strong>UGX 50,000</strong> to earn tokens 🎁
                </div>
            @endif



        <x-filament::button color="primary" tag="a" href="{{ url('/customer/my-orders') }}">
            View Cart ({{ $this->cartCount }})
        </x-filament::button>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-6">
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

                    <div class="mt-4 flex flex-col gap-2">
                        @if (isset($cart[$item->product->id]))
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <x-filament::button 
                                        icon="heroicon-o-minus" 
                                        color="gray" 
                                        size="sm" 
                                        wire:click="decrementQuantity({{ $item->product->id }})" />

                                    <span class="text-sm font-semibold">
                                        {{ $cart[$item->product->id] }} 
                                    </span>

                                    <x-filament::button 
                                        icon="heroicon-o-plus" 
                                        color="gray" 
                                        size="sm" 
                                        wire:click="incrementQuantity({{ $item->product->id }})" />
                                </div>

                                <x-filament::button 
                                    color="danger" 
                                    size="sm" 
                                    wire:click="removeFromCart({{ $item->product->id }})">
                                    Remove from Cart
                                </x-filament::button>
                            </div>
                        @else
                            <x-filament::button 
                                color="primary" 
                                size="sm"
                                wire:click="addToCart({{ $item->product->id }})">
                                Add to Cart
                            </x-filament::button>
                        @endif

                        <x-filament::button 
                            color="gray" 
                            size="sm"
                            wire:click="removeFromWishlist({{ $item->id }})">
                            Remove from Wishlist
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
       