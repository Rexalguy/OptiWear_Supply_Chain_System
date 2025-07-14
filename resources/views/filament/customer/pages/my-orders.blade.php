<x-filament-panels::page>
    <div class="space-y-8">

        {{-- Cart Summary --}}
        @if (!empty($this->cart))
            @php
                $products = \App\Models\Product::whereIn('id', array_keys($this->cart))->get()->keyBy('id');
                $total = 0;
            @endphp

            <section class="border rounded-lg p-6 bg-white dark:bg-gray-800 shadow text-gray-900 dark:text-white">
                <h2 class="text-xl font-bold mb-6 flex items-center space-x-2">
                    <span>🛒 Current Cart</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">({{ count($this->cart) }} {{ Str::plural('item', count($this->cart)) }})</span>
                </h2>

                        @if ($potentialTokens > 0)
            <div class="text-sm text-green-600 dark:text-green-400 text-right">
                🎁 You will earn <strong>{{ $potentialTokens }}</strong> token{{ $potentialTokens > 1 ? 's' : '' }} for this order!
            </div>
            @else
            <div class="text-sm text-green-600 dark:text-green-400 text-right">
                Make a purchase above <strong>UGX 50,000</strong> to earn tokens 🎁
            </div>
            @endif

                <ul class="space-y-5">
                    @foreach ($this->cart as $productId => $qty)
                        @php
                            $product = $products->get($productId);
                            $subtotal = $product ? $product->price * $qty : 0;
                            $total += $subtotal;
                            $maxReached = $qty >= 50 || $qty >= ($product->quantity_available ?? 0);
                        @endphp

                        @if ($product)
                            <li class="flex justify-between items-center border-b pb-4 last:border-b-0">
                                <div class="flex flex-col">
                                    <div class="font-semibold text-lg">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">SKU: {{ $product->sku }}</div>
                                    <div class="text-sm text-gray-700 dark:text-gray-400 mt-1">UGX {{ number_format($product->price) }} × {{ $qty }}</div>

                                    <div class="mt-3 flex items-center space-x-3">
                                        <x-filament::button
                                            size="sm"
                                            color="gray"
                                            wire:click="decreaseQuantity({{ $productId }})"
                                            wire:loading.attr="disabled"
                                            :disabled="$qty <= 1"
                                            aria-label="Decrease quantity"
                                        >-</x-filament::button>

                                        <span class="text-sm font-medium px-2">{{ $qty }}</span>

                                        <x-filament::button
                                            size="sm"
                                            color="gray"
                                            wire:click="increaseQuantity({{ $productId }})"
                                            wire:loading.attr="disabled"
                                            :disabled="$maxReached"
                                            aria-label="Increase quantity"
                                        >+</x-filament::button>
                                    </div>

                                    @if ($maxReached)
                                        <div class="text-xs text-red-600 dark:text-red-400 mt-1">
                                            Max limit ({{ min(50, $product->quantity_available) }}) reached
                                        </div>
                                    @endif
                                </div>

                                <div class="text-right font-semibold text-lg whitespace-nowrap">
                                    {{ number_format($subtotal) }}
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>

            @if ($this->userTokens >= 200)
                <div class="text-green-600 mt-1">
                    🎉 Discount applied: UGX 10,000 (Redeemed 200 tokens)
                </div>

                <div class="text-xl font-extrabold mt-2">
                    Amount to pay: UGX {{ number_format($this->finalAmount) }}
                </div>
            @endif


                <div class="mt-6">
                    <label for="deliveryOption" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Delivery Option</label>
                    <select
                        id="deliveryOption"
                        wire:model="deliveryOption"
                        class="filament-forms-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="pickup">Pickup</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </div>

                @if ($this->deliveryOption === 'delivery')
                    <div class="mt-4">
                        <label for="address" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">Delivery Address</label>
                        <textarea
                            id="address"
                            wire:model.defer="address"
                            rows="4"
                            class="filament-forms-textarea w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="Enter your delivery address"
                        ></textarea>
                    </div>
                @endif

                <div class="text-right mt-6">
                    <x-filament::button
                        color="success"
                        wire:click="placeOrder"
                        wire:loading.attr="disabled"
                       
                    >
                        Place Order
                    </x-filament::button>
                </div>
            </section>
        @else
            <div class="text-center text-gray-500 dark:text-gray-400 py-16">
                Your cart is empty.
            </div>
        @endif


        {{-- Past Orders Table --}}
        <section>
            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">📦 Previous Orders</h2>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                {{ $this->table }}
            </div>
        </section>

    </div>
</x-filament-panels::page>