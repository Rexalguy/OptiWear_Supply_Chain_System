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
                <div class="rounded-xl p-4 shadow bg-white dark:bg-gray-800">

              {{-- Product Image (smaller & constrained) --}}
<div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
    <img src="{{ $product->image }}" 
         alt="{{ $product->name }}"
         style="max-height: 180px; max-width: 90%; object-fit: contain;">
</div>

                    {{-- Product Info --}}
                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                    <p class="text-sm">Price: UGX {{ number_format($product->price) }}</p>

                    {{-- Small "Order" Button --}}
                    <div class="mt-3 flex justify-end">
                        <x-filament::button size="sm" color="primary"
                            wire:click="openProductModal({{ $product->id }})">
                             Order
                        </x-filament::button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Product Modal --}}
    @if ($selectedProduct && $clickedProduct)
        <style>
            .modal-fade-in { animation: modalFadeIn 0.3s ease; }
            @keyframes modalFadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .modal-slide-up { animation: modalSlideUp 0.4s cubic-bezier(.4,0,.2,1); }
            @keyframes modalSlideUp {
                from { transform: translateY(40px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            .close-btn-animate {
                transition: transform 0.2s;
            }
            .close-btn-animate:hover {
                transform: scale(1.2) rotate(90deg);
            }
        </style>

        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 modal-fade-in"
             wire:click.self="closeProductModal">

            <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg p-6 w-full max-w-md relative modal-slide-up"
                 @click.stop>

                {{-- Close Button --}}
                <button class="absolute top-2 right-4 text-gray-400 hover:text-gray-600 text-4xl font-extrabold close-btn-animate"
                        wire:click="closeProductModal" aria-label="Close">
                    &times;
                </button>

{{-- Product Image in Modal (smaller) --}}
<div class="w-full flex items-center justify-center bg-gray-100 rounded-md mb-4 overflow-hidden">
    <img src="{{ $clickedProduct->image }}"
         alt="{{ $clickedProduct->name }}"
         style="max-height: 180px; max-width: 90%; object-fit: contain;">
</div>

                {{-- Product Details --}}
                <h3 class="text-lg font-semibold">{{ $clickedProduct->name }}</h3>
                <p class="text-sm text-gray-500">SKU: {{ $clickedProduct->sku }}</p>
                <p class="text-sm">Price: UGX {{ number_format($clickedProduct->price) }}</p>
                <p class="text-sm text-gray-600">Available: {{ $clickedProduct->quantity_available }}</p>
                <p class="text-sm text-gray-600 mt-2">{{ $clickedProduct->description }}</p>

                {{-- Existing Cart Items for this Product --}}
                @php
                    $clickedProduct = $clickedProduct ?? null;
                    $cartItemsForClicked = collect($this->productCartItems)
                        ->filter(fn($item) => $clickedProduct && isset($item['product']) && $item['product']->id === $clickedProduct->id);
                @endphp

                @foreach ($cartItemsForClicked as $cartKey => $cartItem)
                    <div class="mt-3 flex justify-between items-center border p-2 rounded bg-gray-50 dark:bg-gray-800">
                        <div class="flex flex-col">
                            <span class="text-xs text-gray-500">Size: {{ $cartItem['size'] }}</span>
                            <span class="text-xs text-gray-400">Qty: {{ $cartItem['quantity'] }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            {{-- Decrement --}}
                            <x-filament::button icon="heroicon-o-minus" size="sm"
                                wire:click="decrementQuantity('{{ $cartKey }}')" />
                            {{-- Quantity --}}
                            <span class="text-sm font-semibold">{{ $cartItem['quantity'] }}</span>
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

                {{-- If there are existing sizes, allow adding another size --}}
                @if ($cartItemsForClicked->count() > 0)
                    <x-filament::button color="secondary" class="mt-3"
                        wire:click="requestNewSize({{ $clickedProduct->id }})">
                        + Add Another Size
                    </x-filament::button>
                @endif

                {{-- Size dropdown if requested OR first-time add --}}
                @if ($cartItemsForClicked->isEmpty() || (isset($showSizeDropdown[$clickedProduct->id]) && $showSizeDropdown[$clickedProduct->id]))
                    <div class="mt-3">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Choose Size:</label>
                        <select wire:model="selectedSize.{{ $clickedProduct->id }}"
                                class="w-full border rounded p-2 mt-1 text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-800">
                            <option value="">Select Size</option>
                            @foreach ($sizes as $size)
                                <option value="{{ $size }}">{{ $size }}</option>
                            @endforeach
                        </select>

                        {{-- Confirm add to cart --}}
                        <x-filament::button color="warning" class="mt-3"
                            wire:click="confirmAddToCart({{ $clickedProduct->id }})">
                            Add to Cart
                        </x-filament::button>
                    </div>
                @endif

                {{-- Wishlist & Close --}}
                <div class="flex justify-between mt-4">
                    <x-filament::button
                        :color="in_array($clickedProduct->id, $wishlistProductIds) ? 'danger' : 'gray'"
                        :icon="in_array($clickedProduct->id, $wishlistProductIds) ? 'heroicon-s-heart' : 'heroicon-o-heart'"
                        wire:click="toggleWishlist({{ $clickedProduct->id }})"
                        tooltip="Add/Remove from Wishlist"
                    />
                    <x-filament::button color="secondary" wire:click="closeProductModal">
                        Close
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>