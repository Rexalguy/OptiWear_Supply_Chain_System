<x-filament-panels::page>
    <div class="p-6 space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🛍️ Available Products</h2>

        {{-- Cart token info --}}
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

        {{-- Products Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="p-4 rounded-lg shadow bg-white dark:bg-gray-800">
                    <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/image.png' }}"
                             alt="{{ $product->name }}"
                             class="h-full w-auto object-contain">
                    </div>

                    {{-- Product Info --}}
                    <div>
                        <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</p>
                        <p class="text-sm font-semibold mt-1">UGX {{ number_format($product->price) }}</p>
                        <p class="text-sm mt-1 {{ $product->quantity_available > 10 ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $product->quantity_available }} in stock
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 space-y-3">
                        @php
                            // Filter cart items belonging to this product only (match product->id)
                            $cartItemsForProduct = collect($this->productCartItems)
                                ->filter(fn($item) => $item['product']->id === $product->id);
                        @endphp

                        {{-- Show all existing cart entries for this product (different sizes) --}}
                        @foreach ($cartItemsForProduct as $cartKey => $cartItem)
                            <div class="flex items-center justify-between border p-2 rounded bg-gray-50 dark:bg-gray-700">
                                <div class="flex flex-col">
                                    <span class="text-xs text-gray-500">Size: {{ $cartItem['size'] }}</span>
                                    <span class="text-xs text-gray-400">Qty: {{ $cartItem['quantity'] }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    {{-- Decrement --}}
                                    <x-filament::button icon="heroicon-o-minus" size="sm"
                                        wire:click="decrementQuantity('{{ $cartKey }}')" />

                                    {{-- Quantity --}}
                                    <span class="text-sm font-semibold">
                                        {{ $cartItem['quantity'] }}
                                    </span>

                                    {{-- Increment --}}
                                    <x-filament::button icon="heroicon-o-plus" size="sm"
                                        wire:click="incrementQuantity('{{ $cartKey }}')" />

                                    {{-- Remove --}}
                                    <x-filament::button color="danger" size="sm"
                                        wire:click="removeFromCart('{{ $cartKey }}')">
                                        Remove
                                    </x-filament::button>
                                </div>
                            </div>
                        @endforeach

                        {{-- Show "Add Another Size" if already has cart entries --}}
                        @if ($cartItemsForProduct->count() > 0)
                            <x-filament::button color="secondary" wire:click="requestNewSize({{ $product->id }})">
                                + Add Another Size
                            </x-filament::button>
                        @endif

                        {{-- Show size dropdown if requested --}}
                        @if (isset($showSizeDropdown[$product->id]) && $showSizeDropdown[$product->id])
                            <div class="space-y-2">
                                <select wire:model="selectedSize.{{ $product->id }}"
                                        class="w-full border rounded p-2 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700">
                                    <option value="">Select Size</option>
                                    @foreach ($sizes as $size)
                                        <option value="{{ $size }}">{{ $size }}</option>
                                    @endforeach
                                </select>

                                <x-filament::button color="success"
                                    wire:click="confirmAddToCart({{ $product->id }})">
                                    Confirm & Add
                                </x-filament::button>
                            </div>
                        @endif

                        {{-- Show main Add-to-cart button if no cart entries & no size dropdown --}}
                        @if ($cartItemsForProduct->isEmpty() && !isset($showSizeDropdown[$product->id]))
                            <x-filament::button color="warning" wire:click="addToCart({{ $product->id }})">
                                Add to Cart
                            </x-filament::button>
                        @endif

                        {{-- Wishlist Toggle --}}
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