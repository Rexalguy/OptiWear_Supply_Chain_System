<x-filament-panels::page>
    <div class="p-6 space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🛍️ Available Products</h2>

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
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class=" p-4 rounded-lg shadow bg-white dark:bg-gray-800">
                    <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                        <img style="height: 350px; object-fit: cover;" src="{{ $product->image ? asset('storage/' . $product->image) : '/images/image.png' }}" alt="{{ $product->name }}" class="w-full h-64 object-cover rounded">
                    </div>
                    
                    <div>
                        <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</p>
                        <p class="text-sm font-semibold mt-1">UGX {{ number_format($product->price) }}</p>
                        <p class="text-sm mt-1 {{ $product->quantity_available > 10 ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $product->quantity_available }} in stock
                        </p>
                    </div>

                    <div class="mt-4 flex items-center space-x-2">
                        @if (isset($cart[$product->id]))
                            <x-filament::button icon="heroicon-o-minus" size="sm" wire:click="decrementQuantity({{ $product->id }})" />
                            <span class="text-sm font-semibold">{{ $cart[$product->id] }}</span>
                            <x-filament::button icon="heroicon-o-plus" size="sm" wire:click="incrementQuantity({{ $product->id }})" />
                            <x-filament::button color="danger" size="sm" wire:click="removeFromCart({{ $product->id }})">
                                Remove
                            </x-filament::button>
                        @else
                            <x-filament::button color="warning" wire:click="addToCart({{ $product->id }})">
                                Add to Cart
                            </x-filament::button>
                        @endif

                        <x-filament::button
                            :color="in_array($product->id, $wishlistProductIds) ? 'danger' : 'gray'"
                            :icon="in_array($product->id, $wishlistProductIds) ? 'heroicon-s-heart' : 'heroicon-o-heart'"
                            wire:click="toggleWishlist({{ $product->id }})"
                            tooltip="Add/Remove from Wishlist"
                        />
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>