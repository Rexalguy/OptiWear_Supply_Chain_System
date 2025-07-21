<x-filament-panels::page>
    {{-- tokens progress bar --}}
<div class="mb-8">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">🎯 Token Progress</h2>

    <div class="relative token-progress-track">
        <!-- Progress Fill -->
        <div
            class="token-progress-fill"
            style="width: {{ min(100, ($userTokens / 200) * 100) }}%;"
        ></div>

        <!-- Token Count Text -->
        <div class="absolute inset-0 flex justify-between items-center px-4 text-sm font-medium text-white">
            <span>{{ $userTokens }} / 200 Tokens</span>
            @if ($userTokens >= 200)
                <span class="text-yellow-300 animate-pulse font-bold">🎉 Redeem Now!</span>
            @endif
        </div>

        <!-- Milestone Markers -->
        @foreach ([50, 100, 150, 200] as $milestone)
            <div class="absolute top-full -mt-2 text-xs text-gray-300"
                style="left: {{ ($milestone / 200) * 100 }}%;">
                <div class="w-px h-4 mx-auto bg-gray-500"></div>
                <span class="mt-1 block text-center">{{ $milestone }}</span>
            </div>
        @endforeach
    </div>
</div>



    <div class="space-y-8">
        {{-- CART SUMMARY --}}
        @if (!empty($this->cart) && $this->cartCount > 0)
            @php
                $productIds = collect($this->cart)->pluck('product_id')->unique()->toArray();
                $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');
            @endphp

            <section class="rounded-xl p-6 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white shadow-inner">
                {{-- Cart Header --}}
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-semibold flex items-center gap-2">
                        <span class="bg-gray-100 dark:bg-gray-700 p-2 rounded-lg">🛒</span>
                        <span>
                            Your Cart 
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                ({{ $this->cartCount }} {{ Str::plural('item', $this->cartCount) }})
                            </span>
                        </span>
                    </h2>
                    
                    @if ($potentialTokens > 0)
                        <div class="text-sm bg-green-100/70 dark:bg-green-900/20 text-green-700 dark:text-green-300 px-3 py-1 rounded-full">
                            🎁 Earn {{ $potentialTokens }} token{{ $potentialTokens > 1 ? 's' : '' }}
                        </div>
                    @else
                        <div class="text-sm bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                            Spend UGX 50,000+ to earn tokens
                        </div>
                    @endif
                </div>

                {{-- Cart Items Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    @foreach ($this->cart as $cartKey => $item)
                        @php
                            $product = $products->get($item['product_id']);
                            $quantity = $item['quantity'] ?? 0;
                            $size = $item['size'] ?? '-';
                            $subtotal = $product ? $product->unit_price * $quantity : 0;
                        @endphp

                        @if ($product)
                            <div class="rounded-2xl p-4 bg-white dark:bg-gray-800 shadow-md hover:shadow-lg transition-shadow">
                                <div class="flex gap-4">
                                    <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-xl overflow-hidden flex-shrink-0">
                                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800 dark:text-white">{{ $product->name }}</h3>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                            Size: {{ $size }} • Qty: {{ $quantity }}
                                        </div>
                                        <div class="mt-2 flex items-center justify-between">
                                            <div class="font-semibold text-sky-700 dark:text-sky-300">
                                                UGX {{ number_format($subtotal) }}
                                            </div>
                                            <x-filament::button 
                                                size="md"
                                                color="danger"
                                                icon="heroicon-o-trash"
                                                wire:click="removeFromCart('{{ $cartKey }}')"
                                                class="rounded-md px-3 py-2"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Order Summary --}}
                <div class="mt-6 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                        <span>UGX {{ number_format($this->subtotal) }}</span>
                    </div>

                    {{-- Token Usage Toggle --}}
                    @if ($userTokens > 0)
                        <div class="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-center space-x-2">
                                <input 
                                    type="checkbox" 
                                    id="useTokens" 
                                    wire:model.live="useTokens"
                                    wire:click="toggleTokenUsage"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                >
                                <label for="useTokens" class="text-sm font-medium text-blue-700 dark:text-blue-300">
                                    Use {{ min($userTokens, 200) }} tokens
                                    @if ($userTokens >= 200)
                                        (UGX 10,000 discount)
                                    @else
                                        (UGX {{ number_format($userTokens * 50) }} discount)
                                    @endif
                                </label>
                            </div>
                            <span class="text-blue-600 dark:text-blue-400 font-semibold">🎁</span>
                        </div>
                    @endif

                    {{-- Show discount if tokens are being used --}}
                    @if ($useTokens && $userTokens > 0)
                        <div class="flex justify-between text-green-600 dark:text-green-400">
                            <span>Token Discount</span>
                            <span>- UGX {{ number_format($this->calculatePotentialDiscount()) }}</span>
                        </div>
                    @endif

                    @if ($this->deliveryOption === 'delivery')
                        <div class="flex justify-between">
                            <span class="text-gray-500 dark:text-gray-400">Delivery</span>
                            <span>UGX 5,000</span>
                        </div>
                    @endif

                    <div class="flex justify-between pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-lg font-semibold">Total</span>
                        <span class="text-green-600 dark:text-green-400 text-lg font-semibold">UGX {{ number_format($this->finalAmount) }}</span>
                    </div>
                </div>

                {{-- Delivery Options and Checkout --}}
                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium mb-1">Delivery Option</label>
                        <select 
                            wire:model.live="deliveryOption"
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg text-sm"
                        >
                            <option value="pickup">Pickup</option>
                            <option value="delivery">Delivery</option>
                        </select>
                    </div>

                    <x-filament::button
                        color="primary"
                        icon="heroicon-o-shopping-bag"
                        wire:click="placeOrder"
                        class="w-full justify-center py-3 h-[48px] text-base rounded-lg shadow"
                    >
                        Complete Order • UGX {{ number_format($this->finalAmount) }}
                    </x-filament::button>
                </div>

                @if ($this->deliveryOption === 'delivery')
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">Delivery Address</label>
                        <textarea
                            wire:model.defer="address"
                            rows="3"
                            class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg text-sm"
                            placeholder="Enter your delivery address"
                        ></textarea>
                    </div>
                @endif
            </section>
        @else
            {{-- Empty Cart --}}
            <div class="text-center py-12">
                <div class="mx-auto w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    🛒
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Your cart is empty</h3>
                <p class="text-gray-500 dark:text-gray-400">Add some products to get started</p>
            </div>
        @endif

        {{-- ORDER HISTORY --}}
        <section>
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-gray-100 dark:bg-gray-700 p-2 rounded-lg">
                    📦
                </div>
                <h2 class="text-xl font-semibold">Order History</h2>
            </div>
            
            <div class="rounded-xl overflow-hidden bg-white dark:bg-gray-800">
                {{ $this->table }}
            </div>
        </section>
    </div>
</x-filament-panels::page>
