<x-filament-panels::page>
    <div class="space-y-8">

        {{-- ✅ CART SUMMARY --}}
        @if (!empty($this->cart))
            @php
                $products = \App\Models\Product::whereIn('id', array_keys($this->cart))->get()->keyBy('id');
                $total = 0;
            @endphp

            <section class="border rounded-lg p-6 bg-white dark:bg-gray-800 shadow text-gray-900 dark:text-white">
                <h2 class="text-xl font-bold mb-6 flex items-center space-x-2">
                    <span>🛒 Current Cart</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        ({{ count($this->cart) }} {{ Str::plural('item', count($this->cart)) }})
                    </span>
                </h2>

                {{-- ✅ Token message --}}
                @if ($potentialTokens > 0)
                    <div class="text-sm text-green-600 dark:text-green-400 text-right">
                        🎁 You will earn <strong>{{ $potentialTokens }}</strong> token{{ $potentialTokens > 1 ? 's' : '' }} for this order!
                    </div>
                @else
                    <div class="text-sm text-green-600 dark:text-green-400 text-right">
                        Make a purchase above <strong>UGX 50,000</strong> to earn tokens 🎁
                    </div>
                @endif

                {{-- ✅ CART ITEMS LIST --}}
                <ul class="space-y-5 mt-4">
                    @foreach ($this->cart as $productId => $item)
                        @php
                            $product = $products->get($productId);
                            $quantity = $item['quantity'] ?? 0;
                            $size = $item['size'] ?? '-';
                            $subtotal = $product ? $product->price * $quantity : 0;
                            $total += $subtotal;
                        @endphp

                        @if ($product)
                            <li class="flex justify-between items-center border-b pb-4 last:border-b-0">
                                {{--  Left side: Product info --}}
                                <div class="flex flex-col">
                                    <div class="font-semibold text-lg">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">SKU: {{ $product->sku }}</div>
                                    <div class="text-sm text-gray-700 dark:text-gray-400 mt-1">
                                        Size: <strong>{{ $size }}</strong> | UGX {{ number_format($product->price) }} × {{ $quantity }}
                                    </div>

                                    {{--  Remove button only --}}
                                    <div class="mt-3">
                                        <x-filament::button
                                            size="sm"
                                            color="danger"
                                            wire:click="removeFromCart({{ $productId }})"
                                            wire:loading.attr="disabled"
                                            aria-label="Remove from cart"
                                        >
                                            ❌ Remove from Cart
                                        </x-filament::button>
                                    </div>
                                </div>

                                {{--  Right side: Subtotal --}}
                                <div class="text-right font-semibold text-lg whitespace-nowrap">
                                    UGX {{ number_format($subtotal) }}
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>

                {{--  Discount Info --}}
                @if ($this->userTokens >= 200)
                    <div class="text-green-600 mt-4">
                        🎉 Discount applied: UGX 10,000 (Redeemed 200 tokens)
                    </div>

                    <div class="text-xl font-extrabold mt-2">
                        Amount to pay: UGX {{ number_format($this->finalAmount) }}
                    </div>
                @endif

                {{--  Delivery Option --}}
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

                {{--  Show Address only if delivery --}}
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

                {{--  Place Order --}}
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
            {{-- Empty cart --}}
            <div class="text-center text-gray-500 dark:text-gray-400 py-16">
                Your cart is empty.
            </div>
        @endif

        {{--  PAST ORDERS TABLE --}}
        <section>
            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">📦 Previous Orders</h2>

            <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                {{ $this->table }}
            </div>
        </section>

    </div>
</x-filament-panels::page>