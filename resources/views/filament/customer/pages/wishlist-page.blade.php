<x-filament-panels::page>
    <div class="p-6 space-y-6">       

        {{-- Token info like PlaceOrder --}}
        <div class="mt-6 text-right">
            @if ($potentialTokens > 0)
                <div class="text-sm text-green-600 dark:text-green-400 text-right mb-2">
                    🎁 You will earn <strong>{{ $potentialTokens }}</strong> token{{ $potentialTokens > 1 ? 's' : '' }} for this order!
                </div>
            @else
                <div class="text-sm text-green-600 dark:text-green-400 text-right mb-2">
                    Add items above <strong>UGX 50,000</strong> to earn tokens 🎁
                </div>
            @endif

            <x-filament::button color="primary" tag="a" href="{{ url('/customer/my-orders') }}">
                View Cart ({{ $this->cartCount }})
            </x-filament::button>
        </div>

        {{-- Wishlist Grid --}}
        @if ($wishlistItems->isEmpty())
            <div class="text-center text-gray-500 dark:text-gray-400 py-10">
                Your wishlist is empty 💔 <br>
                <a href="{{ url('/customer/place-order') }}" class="text-primary underline">Browse products</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($wishlistItems as $wishlist)
                    @php $product = $wishlist->product; @endphp
                    @if ($product)
                        <div class="rounded-xl p-4 shadow bg-white dark:bg-gray-800 cursor-pointer relative"
                             wire:click="openProductModal({{ $product->id }})">

                            {{-- Wishlist Remove (top-right) --}}
                            <button class="absolute top-2 right-2 bg-red-100 hover:bg-red-200 text-red-500 rounded-full p-2"
                                    wire:click.stop="removeFromWishlist({{ $wishlist->id }})"
                                    title="Remove from Wishlist">
                                <x-heroicon-o-x-mark class="w-4 h-4"/>
                            </button>

                            {{-- Product Image --}}
                            <div class="w-full h-48 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/image.png' }}"
                                     alt="{{ $product->name }}"
                                     class="h-full w-auto object-contain">
                            </div>

                            {{-- Product Info --}}
                            <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                            <p class="text-sm text-gray-600">Price: UGX {{ number_format($product->price) }}</p>
                            <p class="text-sm {{ $product->quantity_available > 10 ? 'text-green-600' : 'text-yellow-600' }}">
                                Available: {{ $product->quantity_available }}
                            </p>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Product Modal (reused from PlaceOrder) --}}
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

                {{-- Product Image --}}
                <div class="w-full h-48 flex items-center justify-center bg-gray-100 rounded-md mb-4 overflow-hidden">
                    <img src="{{ $clickedProduct->image ? asset('storage/' . $clickedProduct->image) : '/images/image.png' }}"
                         alt="{{ $clickedProduct->name }}"
                         class="h-full w-auto object-contain">
                </div>

                {{-- Product Details --}}
                <h3 class="text-lg font-semibold">{{ $clickedProduct->name }}</h3>
                <p class="text-sm text-gray-500">SKU: {{ $clickedProduct->sku }}</p>
                <p class="text-sm text-gray-600">Price: UGX {{ number_format($clickedProduct->price) }}</p>
                <p class="text-sm text-gray-600">Available: {{ $clickedProduct->quantity_available }}</p>
                <p class="text-sm text-gray-600 mt-2">{{ $clickedProduct->description }}</p>

                {{-- Existing Cart Items for this Product --}}
                @php
                    // Ensure $cart is always defined to avoid errors
                    if (!isset($cart) || !is_array($cart)) {
                        $cart = [];
                    }
                    $cartItemsForClicked = collect($cart)
                        ->filter(function($item) use ($clickedProduct) {
                            return $clickedProduct && isset($item['product_id']) && $item['product_id'] === $clickedProduct->id;
                        });
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
                    {{-- Remove from wishlist directly --}}
                    <x-filament::button color="danger"
                        wire:click="removeFromWishlist({{ $wishlistItems->firstWhere('product_id', $clickedProduct->id)->id ?? 0 }})">
                        Remove from Wishlist
                    </x-filament::button>
                    <x-filament::button color="secondary" wire:click="closeProductModal">
                        Close
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>