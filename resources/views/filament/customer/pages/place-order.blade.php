<x-filament-panels::page>

    <div class="w-full">
        <x-filament::card class="custom-slider-section">
            <div 
                x-data="{
                    currentSlide: 0,
                    slides: 4,
                    interval: null,
                    slideInterval: 5000,
                    init() { this.startAutoPlay(); },
                    startAutoPlay() {
                        this.interval = setInterval(() => {
                            this.next();
                        }, this.slideInterval);
                    },
                    stopAutoPlay() { clearInterval(this.interval); },
                    next() { this.currentSlide = (this.currentSlide + 1) % this.slides; },
                    goTo(index) { this.currentSlide = index; }
                }"
                @mouseenter="stopAutoPlay()" 
                @mouseleave="startAutoPlay()"
                class="relative w-full h-40 sm:h-52 md:h-64 overflow-hidden"
            >
                <!-- Slides -->
                <template x-for="(slide, index) in [
                    {
                        image: 'https://plus.unsplash.com/premium_photo-1688497830977-f9ab9f958ca7?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Nnx8dCUyMHNoaXJ0fGVufDB8MHwwfHx8MQ%3D%3D',
                        title: '🔥 50% OFF',
                        subtitle: 'Limited-time offer on classic shirts'
                    },
                    {
                        image: 'https://images.unsplash.com/photo-1523199455310-87b16c0eed11?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTF8fHNoaXJ0c3xlbnwwfHwwfHx8MA%3D%3D',
                        title: '🆕 New Arrival',
                        subtitle: 'Modern casual collection just dropped'
                    },
                    {
                        image: 'https://plus.unsplash.com/premium_photo-1684952850890-08b775d7bc2e?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8NHx8dCUyMHNoaXJ0fGVufDB8MHwwfHx8MQ%3D%3D',
                        title: '🚚 Free Delivery',
                        subtitle: 'Enjoy free shipping on orders over UGX 150,000'
                    },
                    {
                        image: 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fHNoaXJ0c3xlbnwwfHwwfHx8MA%3D%3D',
                        title: '🎁 Rewards Program',
                        subtitle: 'Earn tokens every time you shop'
                    }
                ]" :key="index">
                    <div 
                         x-show="currentSlide === index"
                         x-transition:enter="transition ease-out duration-700"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-500"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-105"
                         class="absolute inset-0 w-full h-full overflow-hidden rounded-md"
                    >
                        <img :src="slide.image" 
                             class="w-full h-full object-cover transition-transform duration-700"
                             :class="{'scale-105': currentSlide === index}" 
                             alt="Slide image" />
                        <div class="absolute inset-0 flex items-end p-6 sm:p-8 pointer-events-none">
                            <!-- Bottom shadow overlay -->
                            <div class="absolute bottom-0 left-0 right-0 h-24 bg-gradient-to-t from-black/60 to-transparent"></div>
                            <!-- Text content -->
                            <div class="relative z-10 bg-black/40 backdrop-blur-md px-4 py-3 rounded-md shadow-lg max-w-sm">
                                <h3 x-text="slide.title"
                                    class="text-white text-xl sm:text-2xl font-bold leading-tight tracking-wide"
                                ></h3>
                                <p x-text="slide.subtitle"
                                   class="text-white/90 text-sm sm:text-base mt-1"
                                ></p>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Dot Indicators -->
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex space-x-3 z-20">
                    <template x-for="i in slides" :key="i">
                        <button
                            @click="goTo(i - 1); stopAutoPlay()"
                            :class="currentSlide === i - 1 
                                    ? 'w-6 h-1 bg-white shadow-md rounded-sm transition-all duration-300' 
                                    : 'w-4 h-1 bg-white/50 hover:bg-white rounded-sm transition-all duration-300'"
                            class="cursor-pointer transition-all"
                            aria-label="Go to slide"
                        ></button>
                    </template>
                </div>
            </div>
        </x-filament::card>
    </div>


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
                    <p class="text-sm">Price: UGX {{ number_format($product->unit_price) }}</p>

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
                <p class="text-sm">Price: UGX {{ number_format($clickedProduct->unit_price) }}</p>
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