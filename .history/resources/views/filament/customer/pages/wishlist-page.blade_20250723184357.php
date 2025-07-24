<x-filament-panels::page>
    <script src="{{ asset('js/sweetalert-handler.js') }}"></script>
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

        /* Enhanced wishlist grid spacing */
        .wishlist-grid {
            gap: 1.5rem;
        }
        
        /* Enhanced wishlist card styling */
        .wishlist-card {
            background: linear-gradient(to bottom right, #ffffff, #f8fafc);
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .dark .wishlist-card {
            background: linear-gradient(to bottom right, #1f2937, #111827);
            border: 1px solid #374151;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .wishlist-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        /* Image zoom effect for wishlist items */
        .wishlist-image-container {
            overflow: hidden;
            border-radius: 0.5rem;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
        }
        
        .dark .wishlist-image-container {
            background: #374151;
            border: 1px solid #4b5563;
        }
        
        .wishlist-image-container img {
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .wishlist-image-container:hover img {
            transform: scale(1.05);
        }

        /* Enhanced token section */
        .token-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid #e2e8f0;
            backdrop-filter: blur(10px);
        }
        
        .dark .token-section {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border: 1px solid #475569;
        }

        /* Remove button styling */
        .remove-btn {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            border: 1px solid #fca5a5;
            color: #dc2626;
            transition: all 0.2s ease;
        }

        .remove-btn:hover {
            background: linear-gradient(135deg, #fecaca, #f87171);
            color: #991b1b;
            transform: scale(1.1);
        }

        .dark .remove-btn {
            background: linear-gradient(135deg, #7f1d1d, #991b1b);
            border: 1px solid #dc2626;
            color: #fca5a5;
        }

        .dark .remove-btn:hover {
            background: linear-gradient(135deg, #991b1b, #b91c1c);
            color: #fee2e2;
        }

        /* Enhanced product name styling */
        .product-name {
            line-height: 1.4;
            font-weight: 600;
            letter-spacing: -0.015em;
            color: #1f2937;
        }

        .dark .product-name {
            color: #f9fafb;
        }

        /* Enhanced price styling */
        .price-text {
            font-size: 1.125rem;
            font-weight: 700;
            color: #10b981;
            letter-spacing: -0.025em;
        }

        /* Empty state styling */
        .empty-state {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px dashed #cbd5e1;
            border-radius: 1rem;
            padding: 3rem 2rem;
        }

        .dark .empty-state {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border: 2px dashed #475569;
        }
    </style>

    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $wishlistItems->count() }} item{{ $wishlistItems->count() !== 1 ? 's' : '' }}
            </div>
        </div>

        {{-- Enhanced Token info section --}}
        <div class="token-section flex flex-col md:flex-row justify-between items-center p-6 rounded-xl shadow-sm">
            <div class="text-base md:text-lg text-gray-800 dark:text-gray-200 mb-4 md:mb-0 font-medium">
                @if ($potentialTokens > 0)
                    🎁 You will earn <strong class="text-primary-600 dark:text-primary-400 font-bold">{{ $potentialTokens }}</strong> token{{ $potentialTokens > 1 ? 's' : '' }} for this order!
                @else
                    Add items above <strong class="text-primary-600 dark:text-primary-400 font-bold">UGX 50,000</strong> to earn tokens 🎁
                @endif
            </div>

            <a href="{{ url('/customer/my-orders') }}" class="relative inline-flex items-center gap-2 px-6 py-3.5 text-base font-semibold text-white bg-gradient-to-r from-sky-500 to-sky-700 rounded-xl transition-all duration-300 hover:from-sky-600 hover:to-sky-800 hover:shadow-lg hover:scale-105">
                <x-heroicon-o-shopping-cart class="w-5 h-5" />
                View Cart

                @if ($this->cartCount > 0)
                    <span class="absolute -top-2 -right-2 flex items-center justify-center w-6 h-6 text-xs font-bold text-white bg-red-500 rounded-full shadow-lg animate-pulse">
                        {{ $this->cartCount }}
                    </span>
                @endif
            </a>
        </div>

        {{-- Wishlist Grid --}}
        @if ($wishlistItems->isEmpty())
            <div class="empty-state text-center text-gray-500 dark:text-gray-400">
                <div class="mb-4">
                    <x-heroicon-o-heart class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600" />
                </div>
                <h3 class="text-xl font-semibold mb-2">Your wishlist is empty</h3>
                <p class="text-gray-400 dark:text-gray-500 mb-4">
                    Start adding products you love to your wishlist
                </p>
                <a href="{{ url('/customer/place-order') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-all duration-300 hover:scale-105">
                    <x-heroicon-o-shopping-bag class="w-5 h-5" />
                    Browse Products
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 wishlist-grid">
                @foreach ($wishlistItems as $wishlist)
                    @php $product = $wishlist->product; @endphp
                    @if ($product)
                        <div class="wishlist-card">

                            {{-- Wishlist Remove (top-right) --}}
                            <button class="remove-btn absolute top-3 right-3 rounded-full p-2 z-10"
                                    wire:click.stop="removeFromWishlist({{ $wishlist->id }})"
                                    title="Remove from Wishlist">
                                <x-heroicon-o-x-mark class="w-4 h-4"/>
                            </button>

                            {{-- Enhanced Product Image --}}
                            <div class="wishlist-image-container w-full h-48 flex items-center justify-center mb-4">
                                <img src="{{ asset($product->image) }}" 
                                     alt="{{ $product->name }}"
                                     class="max-h-full max-w-full object-contain">
                            </div>

                            {{-- Enhanced Product Info --}}
                            <div class="space-y-3">
                                <h3 class="product-name text-lg">{{ $product->name }}</h3>                           
                                <p class="price-text">UGX {{ number_format($product->unit_price) }}</p>                            

                                {{-- Enhanced Order Button --}}
                                <x-filament::button 
                                    size="sm" 
                                    color="primary"
                                    class="w-full rounded-lg"
                                    wire:click="openProductModal({{ $product->id }})">
                                    <x-heroicon-o-shopping-cart class="w-4 h-4 mr-2" />
                                    Add to Cart
                                </x-filament::button>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>

    {{-- Enhanced Product Modal --}}
    @if ($selectedProduct && $clickedProduct)
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 modal-fade-in"
             wire:click.self="closeProductModal">

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl max-w-2xl w-full mx-4 relative modal-slide-up border border-gray-200 dark:border-gray-700"
                 @click.stop>

                {{-- Close Button --}}
                <button class="absolute top-3 right-4 text-gray-400 hover:text-gray-600 text-3xl font-extrabold close-btn-animate z-10"
                        wire:click="closeProductModal" aria-label="Close">
                    &times;
                </button>

                <div class="p-6">
                    {{-- Enhanced Product Image in Modal --}}
                    <div class="w-full h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-xl mb-6 overflow-hidden">
                        <img src="{{ asset($clickedProduct->image) }}"
                             alt="{{ $clickedProduct->name }}"
                             class="max-h-full max-w-full object-contain">
                    </div>

                    {{-- Enhanced Product Details --}}
                    <div class="space-y-4">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $clickedProduct->name }}</h3>
                        <p class="text-xl font-bold text-green-600 dark:text-green-400">UGX {{ number_format($clickedProduct->unit_price) }}</p>
                        <p class="text-gray-600 dark:text-gray-300">{{ $clickedProduct->description }}</p>
                    </div>

                {{-- Existing Cart Items for this Product --}}
                @php
                    if (!isset($cart) || !is_array($cart)) {
                        $cart = [];
                    }
                    // Ensure $clickedProduct is assigned
                    $clickedProduct = $clickedProduct ?? null;
                    $cartItemsForClicked = collect($cart)
                        ->filter(fn($item) => isset($item['product_id']) && $clickedProduct && $item['product_id'] === $clickedProduct->id);
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
                    @php
                        $wishlistId = $wishlistItems->firstWhere('product_id', $clickedProduct->id)->id ?? null;
                    @endphp
                    @if ($wishlistId)
                        <x-filament::button color="danger"
                            wire:click="removeFromWishlist({{ $wishlistId }})">
                            Remove from Wishlist
                        </x-filament::button>
                    @endif
                    <x-filament::button color="secondary" wire:click="closeProductModal">
                        Close
                    </x-filament::button>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>