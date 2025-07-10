<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Cart Summary --}}
        @if (!empty($this->cart))
            @php
                $products = \App\Models\Product::whereIn('id', array_keys($this->cart))->get()->keyBy('id');
                $total = 0;
            @endphp

            <div class="border rounded-lg p-6 bg-white dark:bg-gray-800 shadow text-gray-900 dark:text-white">
                <h2 class="text-lg font-bold mb-4">🛒 Current Cart</h2>

                <ul class="space-y-4">
                    @foreach ($this->cart as $productId => $qty)
                        @php
                            $product = $products->get($productId);
                            $subtotal = $product ? $product->price * $qty : 0;
                            $total += $subtotal;
                            $maxReached = $qty >= 50 || $qty >= ($product->quantity_available ?? 0);
                        @endphp

                        @if ($product)
                            <li class="flex justify-between items-center">
                                <div>
                                    <div class="font-medium">{{ $product->name }}</div>
                                    <div class="text-sm">SKU: {{ $product->sku }}</div>
                                    <div class="text-sm">UGX {{ number_format($product->price) }} × {{ $qty }}</div>

                                    <div class="mt-2 flex items-center space-x-2">
                                        <x-filament::button
                                            size="sm"
                                            color="gray"
                                            wire:click="decreaseQuantity({{ $productId }})"
                                            wire:loading.attr="disabled"
                                            :disabled="$qty <= 1"
                                        >-</x-filament::button>

                                        <span class="text-sm">{{ $qty }}</span>

                                        <x-filament::button
                                            size="sm"
                                            color="gray"
                                            wire:click="increaseQuantity({{ $productId }})"
                                            wire:loading.attr="disabled"
                                            :disabled="$maxReached"
                                        >+</x-filament::button>
                                    </div>

                                    @if ($maxReached)
                                        <div class="text-xs text-red-500 mt-1">
                                            Max limit ({{ min(50, $product->quantity_available) }}) reached
                                        </div>
                                    @endif
                                </div>

                                <div class="text-right font-semibold">
                                    UGX{{ number_format($subtotal) }}
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>

                <div class="border-t pt-4 mt-4 flex justify-between font-bold text-lg">
                    <span>Total:</span>
                    <span>UGX {{ number_format($total) }}</span>
                </div>

                <div class="mt-6">
                    <label for="deliveryOption" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Delivery Option</label>
                    <select id="deliveryOption" wire:model="deliveryOption" class="filament-forms-select w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                        <option value="pickup">Pickup</option>
                        <option value="delivery">Delivery</option>
                    </select>
                </div>

                {{-- Show address field if delivery is selected --}}
                @if ($this->deliveryOption === 'delivery')
                    <div class="mt-4">
                        <label for="address" class="block font-medium text-sm text-gray-700 dark:text-gray-300 mb-1">Delivery Address</label>
                        <textarea
                            id="address"
                            wire:model.defer="address"
                            rows="3"
                            class="filament-forms-textarea w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                            placeholder="Enter your delivery address"
                        ></textarea>
                    </div>
                @endif

                <div class="text-right mt-4">
                    <x-filament::button
                        color="success"
                        wire:click="placeOrder"
                        wire:loading.attr="disabled"
                    >
                        Place Order
                    </x-filament::button>
                </div>
            </div>
        @else
            <div class="text-center text-gray-500 dark:text-gray-400 py-10">
                Your cart is empty.
            </div>
        @endif

        {{-- Past Orders Table --}}
        <div>
            <h2 class="text-lg font-bold mb-2 text-gray-900 dark:text-white">📦 Previous Orders</h2>
            {{ $this->table }}
        </div>

    </div>
</x-filament-panels::page>