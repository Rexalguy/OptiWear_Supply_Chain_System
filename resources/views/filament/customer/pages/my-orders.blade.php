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
                        @endphp

                        @if ($product)
                            <li class="flex justify-between items-center">
                                <div>
                                    <div class="font-medium">{{ $product->name }}</div>
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        UGX {{ number_format($product->price) }} × {{ $qty }}
                                    </div>
                                </div>
                                <div class="text-right font-semibold">
                                    UGX {{ number_format($subtotal) }}
                                </div>
                            </li>
                        @endif
                    @endforeach
                </ul>

                <div class="border-t pt-4 mt-4 flex justify-between font-bold text-lg">
                    <span>Total:</span>
                    <span>UGX {{ number_format($total) }}</span>
                </div>

                <div class="text-right mt-4">
                    <x-filament::button color="success" wire:click="placeOrder" wire:loading.attr="disabled">
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