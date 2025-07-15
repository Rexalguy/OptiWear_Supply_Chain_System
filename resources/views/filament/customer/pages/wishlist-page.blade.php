<x-filament-panels::page>
    <div class="p-6 space-y-6">

        {{-- Token Info --}}
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

        {{-- Wishlist Items --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-6">
            @forelse ($wishlistItems as $item)
                @php 
                    $product = $item->product;

                    // Find all cart entries for this product (may have multiple sizes)
                    $productCartEntries = collect($cart)
                        ->filter(fn($entry) => $entry['product_id'] === $product->id);
                @endphp

                <div class="border p-4 rounded-lg shadow bg-white dark:bg-gray-800 flex flex-col justify-between">
                    <div>
                        <img src="{{ $product->image ? asset('storage/'.$product->image) : '/images/image.png' }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-48 object-cover rounded mb-4">

                        <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            UGX {{ number_format($product->price) }}
                        </p>
                        <p class="text-xs mt-1 {{ $product->quantity_available > 10 ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $product->quantity_available }} in stock
                        </p>
                    </div>

                    <div class="mt-4 flex flex-col gap-3">

                        {{--  Existing cart entries (show qty per size) --}}
                        @foreach ($productCartEntries as $cartKey => $entry)
                            <div class="border rounded p-2 text-sm flex flex-col gap-2">
                                <div class="flex justify-between">
                                    <span class="font-medium">
                                         Size: {{ $entry['size'] }}
                                    </span>

                                    {{-- Remove this size --}}
                                    <x-filament::button 
                                        color="danger" 
                                        size="xs" 
                                        wire:click="removeFromCart('{{ $cartKey }}')">
                                        Remove
                                    </x-filament::button>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        {{-- Decrement this size --}}
                                        <x-filament::button 
                                            icon="heroicon-o-minus" 
                                            color="gray" 
                                            size="xs" 
                                            wire:click="decrementQuantity('{{ $cartKey }}')" />

                                        <span class="font-semibold">
                                            {{ $entry['quantity'] }}
                                        </span>

                                        {{-- Increment this size --}}
                                        <x-filament::button 
                                            icon="heroicon-o-plus" 
                                            color="gray" 
                                            size="xs" 
                                            wire:click="incrementQuantity('{{ $cartKey }}')" />
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{--  If size dropdown is triggered --}}
                        @if (isset($showSizeDropdown[$product->id]) && $showSizeDropdown[$product->id])
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Select Size
                                </label>
                                <select 
                                    wire:model.defer="selectedSize.{{ $product->id }}" 
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                >
                                    <option value="">Select size</option>
                                    @foreach($sizes as $size)
                                        <option value="{{ $size }}">{{ $size }}</option>
                                    @endforeach
                                </select>

                                <div class="flex justify-between mt-2">
                                    <x-filament::button 
                                        color="success" 
                                        size="sm"
                                        wire:click="confirmAddToCart({{ $product->id }})">
                                         Confirm Add
                                    </x-filament::button>

                                    <x-filament::button 
                                        color="gray" 
                                        size="sm"
                                        wire:click="$set('showSizeDropdown.{{ $product->id }}', false)">
                                        ❌ Cancel
                                    </x-filament::button>
                                </div>
                            </div>
                        @else
                            {{--  Button to add new size --}}
                            <x-filament::button 
                                color="{{ $productCartEntries->count() ? 'secondary' : 'primary' }}" 
                                size="sm"
                                wire:click="addToCart({{ $product->id }})">
                                {{ $productCartEntries->count() ? '+ Add Another Size' : 'Add to Cart' }}
                            </x-filament::button>
                        @endif

                        {{--  Always allow remove from wishlist --}}
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