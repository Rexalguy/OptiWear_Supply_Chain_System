<x-filament-panels::page>


    <div class="space-y-8">

        {{-- CART SUMMARY --}}
        @if (!empty($this->cart) && $this->cartCount > 0)
            @php
                $productIds = collect($this->cart)->pluck('product_id')->unique()->toArray();
                $products = \App\Models\Product::whereIn('id', $productIds)->get()->keyBy('id');
            @endphp

            <section class="border rounded-lg p-6 bg-white dark:bg-gray-800 shadow text-gray-900 dark:text-white">
                <h2 class="text-xl font-bold mb-6 flex items-center space-x-2">
                    <span>🛒 Current Cart</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        ({{ $this->cartCount }} {{ Str::plural('item', $this->cartCount) }})
                    </span>
                </h2>

                {{--  Token message --}}
                @if ($potentialTokens > 0)
                    <div class="text-sm text-green-600 dark:text-green-400 text-right">
                        🎁 You will earn <strong>{{ $potentialTokens }}</strong> token{{ $potentialTokens > 1 ? 's' : '' }} for this order!
                    </div>
                @else
                    <div class="text-sm text-green-600 dark:text-green-400 text-right">
                        Make a purchase above <strong>UGX 50,000</strong> to earn tokens 🎁
                    </div>
                @endif

                {{--  CART ITEMS --}}
                <ul class="space-y-5 mt-4">
                    @foreach ($this->cart as $cartKey => $item)
                        @php
                            $product = $products->get($item['product_id']);
                            $quantity = $item['quantity'] ?? 0;
                            $size = $item['size'] ?? '-';
                            $subtotal = $product ? $product->unit_price * $quantity : 0;
                        @endphp

                        @if ($product)
                            <li class="flex justify-between items-center border-b pb-4 last:border-b-0">
                                <div class="flex flex-col">
                                    <div class="font-semibold text-lg">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-300">SKU: {{ $product->sku }}</div>
                                    <div class="text-sm text-gray-700 dark:text-gray-400 mt-1">
                                        Size: <strong>{{ $size }}</strong> | UGX {{ number_format($product->unit_price) }} x {{ $quantity }}
                                    </div>

                                    {{-- Remove --}}
                                    <div class="mt-3">
                                        <x-filament::button
                                            size="sm"
                                            color="danger"
                                            wire:click="removeFromCart('{{ $cartKey }}')"
                                            wire:loading.attr="disabled"
                                        >
                                            Remove
                                        </x-filament::button>
                                    </div>
                                </div>

                                {{-- Right: Subtotal --}}
                                <div class="text-right font-semibold text-lg whitespace-nowrap">
                                    UGX {{ number_format($subtotal) }}
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>

                {{-- SUMMARY SECTION --}}
                <div class="mt-6 border-t pt-4 space-y-3 text-right">
                    <div class="flex justify-between text-lg">
                        <span class="font-medium">Subtotal:</span>
                        <span>UGX {{ number_format($this->subtotal) }}</span>
                    </div>

                    {{-- Delivery Fee dynamically appears --}}
                    @if ($this->deliveryOption === 'delivery')
                        <div class="flex justify-between text-lg">
                            <span>Delivery Fee:</span>
                            <span>+ UGX {{ number_format(5000) }}</span>
                        </div>
                    @endif

                    {{-- Live-updating Total --}}
                    <div class="flex justify-between text-2xl font-bold border-t pt-4">
                        <span>Total Amount:</span>
                        <span class="text-green-600">UGX {{ number_format($this->finalAmount) }}</span>
                    </div>
                </div>

                {{-- Delivery Option --}}
                <div class="mt-6">
                    <label for="deliveryOption" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                        Delivery Option
                    </label>
                    <select
                        id="deliveryOption"
                        wire:model.live="deliveryOption"
                        class="filament-forms-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >
                        <option value="pickup">Pickup</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </div>

                {{-- Address if delivery --}}
                @if ($this->deliveryOption === 'delivery')
                    <div class="mt-4">
                        <label for="address" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-2">
                            Delivery Address
                        </label>
                        <textarea
                            id="address"
                            wire:model.defer="address"
                            rows="4"
                            class="filament-forms-textarea w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="Enter your delivery address"
                        ></textarea>
                    </div>
                @endif

                {{-- Place Order --}}
                <div class="text-right mt-6">
                    <x-filament::button
                        color="success"
                        wire:click="placeOrder"
                        wire:loading.attr="disabled"
                    >
                        Place Order (UGX {{ number_format($this->finalAmount) }})
                    </x-filament::button>
                </div>
            </section>
        @else
            {{-- Empty Cart --}}
            <div class="text-center text-gray-500 dark:text-gray-400 py-16">
                🛒 Your cart is empty.
            </div>
        @endif

        {{-- PREVIOUS ORDERS --}}
        <section>
            <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-white">📦 Previous Orders</h2>
            <div class="overflow-x-auto rounded-lg shadow">
                {{ $this->table }}
            </div>
        </section>

    </div>
</x-filament-panels::page>
