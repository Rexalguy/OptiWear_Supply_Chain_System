<x-filament-panels::page>
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
        /* Custom scrollbar for cart items */
        .cart-items-container::-webkit-scrollbar {
            width: 6px;
        }
        .cart-items-container::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }
        .dark .cart-items-container::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
        
        /* Image container background - changed to white */
        .image-container {
            background-color: #ffffff; /* White background */
        }
        .dark .image-container {
            background-color: #ffffff; /* Keep dark slate in dark mode */
        }

        /* Price styling */
        .price-container {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin: 0.5rem 0;
        }
        .current-price {
            font-size: 1.25rem;
            font-weight: 600;
            color: #10b981; /* Green for current price */
        }
        .original-price {
            font-size: 1rem;
            color: #9ca3af;
            text-decoration: line-through;
        }
        .discount-badge {
            background-color: #ef4444;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>

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
                class="relative w-full h-90 sm:h-52 md:h-64 overflow-hidden"
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
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Available Products</h2>

        <!-- Category Tabs -->
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="category-nav flex space-x-4 overflow-x-auto pb-1" aria-label="Categories">
                <!-- All Categories Tab -->
                <button
                    wire:click="$set('selectedCategory', '')"
                    class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm rounded-t-lg transition-colors duration-200 {{ $selectedCategory === '' ? 'tab-active border-primary-500 text-primary-600 dark:text-primary-400 dark:border-primary-400 bg-primary-50 dark:bg-gray-800' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}"
                >
                    All Products
                </button>
                
                <!-- Category Tabs -->
                @foreach($this->categories as $category)
                    <button
                        wire:click="$set('selectedCategory', '{{ $category->id }}')"
                        class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm rounded-t-lg transition-colors duration-200 {{ $selectedCategory == $category->id ? 'tab-active border-primary-500 text-primary-600 dark:text-primary-400 dark:border-primary-400 bg-primary-50 dark:bg-gray-800' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}"
                    >
                        {{ $category->category }}
                    </button>
                @endforeach
            </nav>
        </div>

        {{-- Cart token info --}}
<div class="token-cart-wrapper flex flex-col md:flex-row justify-between items-center mb-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-md shadow-sm">
    <div class="token-message text-base md:text-lg text-gray-800 dark:text-gray-200 mb-3 md:mb-0 font-medium">
        @if ($potentialTokens > 0)
            🎁 You will earn <strong class="text-primary-600 dark:text-primary-400">{{ $potentialTokens }}</strong> token{{ $potentialTokens > 1 ? 's' : '' }} for this order!
        @else
            Make a purchase above <strong class="text-primary-600 dark:text-primary-400">UGX 50,000</strong> to earn tokens 🎁
        @endif
    </div>

    <a href="{{ url('/customer/my-orders') }}" class="relative inline-flex items-center gap-2 px-5 py-3 text-base font-semibold text-white bg-gradient-to-r from-sky-500 to-sky-700 rounded-md transition hover:from-sky-600 hover:to-sky-800 custom-cart-btn">
        <x-heroicon-o-shopping-cart class="w-5 h-5" />
        View Cart

        @if ($this->cartCount > 0)
            <span class="cart-badge absolute top-0 right-0 -mt-2 -mr-2 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">
                {{ $this->cartCount }}
            </span>
        @endif
    </a>
</div>

        <!-- Products Grid -->
        @if($products->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                    <x-heroicon-o-exclamation-circle class="h-8 w-8 text-gray-400" />
                </div>
                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No products found</h3>
                <p class="mt-1 text-gray-500 dark:text-gray-400">
                    @if($selectedCategory)
                        No products available in this category.
                    @else
                        No products available at the moment.
                    @endif
                </p>
                @if($selectedCategory)
                    <div class="mt-6">
                        <x-filament::button wire:click="$set('selectedCategory', '')" color="gray">
                            Clear filter
                        </x-filament::button>
                    </div>
                @endif
            </div>
        @else
            {{-- Products Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach ($products as $product)
        <div class="product-card dark-gradient-card rounded-lg overflow-hidden">

            {{-- Image Section with Overlay --}}
            <div class="product-image-container relative w-full">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="product-image w-full h-48 object-cover" />
                <div class="top-quality-overlay">New Stock</div>
            </div>

            <h3 class="text-lg font-semibold tracking-wide text-gray-800 dark:text-gray-100 px-4 pt-3">
                {{ $product->name }}
            </h3>

            <p class="text-lg font-bold text-gray-900 dark:text-gray-200 px-4">
                 UGX {{ number_format($product->unit_price) }}
            </p>

            <div class="mt-4 px-4 pb-4">
                <x-filament::button
                    size="sm"
                    color="primary"
                    class="custom-order-btn full-width-animated-btn"
                    wire:click="openProductModal({{ $product->id }})"
                    icon="heroicon-o-shopping-cart"
                >
                    Order Now
                </x-filament::button>
            </div>
        </div>
        @endforeach
        </div>
        @endif

    </div>

    {{-- Product Modal --}}
    @if ($selectedProduct && $clickedProduct)
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 modal-fade-in"
             wire:click.self="closeProductModal">

        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-2xl w-full mx-4 relative modal-slide-up"
             @click.stop>

            {{-- Close Button --}}
            <button class="absolute top-2 right-4 text-gray-400 hover:text-gray-600 text-4xl font-extrabold close-btn-animate"
                    wire:click="closeProductModal" aria-label="Close">
                &times;
            </button>

            <div class="flex flex-col md:flex-row">
                {{-- Left: Product Image (25% width) --}}
                <div class="w-full md:w-1/4 p-4 flex items-center justify-center image-container">
                    <img src="{{ $clickedProduct->image }}"
                         alt="{{ $clickedProduct->name }}"
                         class="max-h-[400px] w-auto object-contain" />
                </div>

                {{-- Right: Product Details and Controls (75% width) --}}
                <div class="w-full md:w-3/4 p-6 space-y-4">
                    {{-- Product Info --}}
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $clickedProduct->name }}</h3>
                    {{-- Price with Dummy Discount --}}
                    <div class="price-container">
                        <span class="current-price">UGX {{ number_format($clickedProduct->unit_price) }}</span>
                        @php
                            // Calculate dummy original price (20% higher than current)
                            $dummyOriginalPrice = $clickedProduct->unit_price * 1.2;
                        @endphp
                        <span class="original-price">UGX {{ number_format($dummyOriginalPrice) }}</span>
                        <span class="discount-badge">
                            20% OFF
                        </span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300">{{ $clickedProduct->description }}</p>

                    {{-- Existing Cart Items for this Product --}}
                    @php
                        $clickedProduct = $clickedProduct ?? null;
                        $cartItemsForClicked = collect($this->productCartItems)
                            ->filter(fn($item) => $clickedProduct && isset($item['product']) && $item['product']->id === $clickedProduct->id);
                    @endphp

                    @if ($cartItemsForClicked->count() > 0)
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Items in your cart:</h4>
                            <div class="cart-items-container max-h-48 overflow-y-auto">
                                @foreach ($cartItemsForClicked as $cartKey => $cartItem)
                                    <div class="flex justify-between items-center p-3 rounded bg-gray-50 dark:bg-gray-700 mb-2 last:mb-0">
                                        <div>
                                            <span class="block text-sm font-medium">Size: {{ $cartItem['size'] }}</span>
                                            <span class="block text-xs text-gray-500 dark:text-gray-400">Qty: {{ $cartItem['quantity'] }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            {{-- Decrement --}}
                                            <x-filament::button icon="heroicon-o-minus" size="sm"
                                                wire:click="decrementQuantity('{{ $cartKey }}')" />
                                            {{-- Quantity --}}
                                            <span class="text-sm font-semibold px-2">{{ $cartItem['quantity'] }}</span>
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
                            </div>
                        </div>
                    @endif

                    {{-- Add Another Size Button --}}
                    @if ($cartItemsForClicked->count() > 0)
                        <x-filament::button color="secondary" class="mt-4 w-full"
                            wire:click="requestNewSize({{ $clickedProduct->id }})">
                            + Add Another Size
                        </x-filament::button>
                    @endif

                    {{-- Size Dropdown & Confirm Add --}}
                    @if ($cartItemsForClicked->isEmpty() || (isset($showSizeDropdown[$clickedProduct->id]) && $showSizeDropdown[$clickedProduct->id]))
                        <div class="mt-4 space-y-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Choose Size:</label>
                            <select wire:model="selectedSize.{{ $clickedProduct->id }}"
                                    class="w-full border rounded-lg p-2 mt-1 text-gray-900 dark:text-white bg-white dark:bg-gray-800 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Size</option>
                                @foreach ($sizes as $size)
                                    <option value="{{ $size }}">{{ $size }}</option>
                                @endforeach
                            </select>

                            <x-filament::button color="primary" class="mt-3 w-full py-3"
                                wire:click="confirmAddToCart({{ $clickedProduct->id }})">
                                Add to Cart
                            </x-filament::button>
                        </div>
                    @endif

                    {{-- Wishlist & Close Buttons --}}
                    <div class="flex justify-between gap-4 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <x-filament::button
                            color="danger" 
                            :icon="in_array($clickedProduct->id, $wishlistProductIds) ? 'heroicon-s-heart' : 'heroicon-o-heart'"
                            wire:click="toggleWishlist({{ $clickedProduct->id }})"
                            class="flex-1"
                            tooltip="Add/Remove from Wishlist"
                        >
                            {{ in_array($clickedProduct->id, $wishlistProductIds) ? 'Remove from Wishlist' : 'Add to Wishlist' }}
                        </x-filament::button>
                        <x-filament::button color="gray" wire:click="closeProductModal" class="flex-1">
                            Close
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

</x-filament-panels::page>