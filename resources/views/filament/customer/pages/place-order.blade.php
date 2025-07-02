<x-filament-panels::page>
    <div class="space-y-6">

         {{-- View Cart Button --}}
       {{-- @if (!empty($cart))
            <div class="text-right">
                <x-filament::button
                    wire:click="openCartModal"
                    icon="heroicon-o-shopping-cart"
                    color="primary"
                >
                    View Cart ({{ array_sum($cart) }})
                </x-filament::button>
            </div>
        @endif--}}
        <div class="text-right">
        <x-filament::button
    tag="a"
    href="/customer/my-orders"
    icon="heroicon-o-shopping-cart"
    color="primary"
>
    View Cart ({{ array_sum($this->cart) }})
</x-filament::button>
        </div>

        {{-- Page Heading --}}

        {{-- Product Table --}}
        {{ $this->table }}

       
        {{-- Cart Summary Modal --}}
        <x-filament::modal
            wire:model="showCartSummaryModal"
            heading="🛒 Your Cart"
            max-width="2xl"
        >
            <div class="space-y-4">
                @if (!empty($cart))
                    @php
                        // Cache products for cart items to avoid multiple queries in loop
                        $cartProducts = \App\Models\Product::whereIn('id', array_keys($cart))->get()->keyBy('id');
                    @endphp

                    <ul class="space-y-4">
                        @foreach ($cart as $productId => $qty)
                            @php $product = $cartProducts->get($productId); @endphp
                            @if ($product)
                                <li class="flex justify-between items-center border-b pb-2">
                                    <div>
                                        <div class="font-semibold">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-600">
                                            UGX {{ number_format($product->price) }} × {{ $qty }}
                                            = UGX {{ number_format($product->price * $qty) }}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        {{-- Decrease --}}
                                        <x-filament::button
                                            size="sm"
                                            color="gray"
                                            icon="heroicon-o-minus"
                                            wire:click="decrementQuantity({{ $productId }})"
                                        />
                                        <span class="text-lg font-semibold">{{ $qty }}</span>
                                        {{-- Increase --}}
                                        <x-filament::button
                                            size="sm"
                                            color="gray"
                                            icon="heroicon-o-plus"
                                            wire:click="incrementQuantity({{ $productId }})"
                                        />
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    {{-- Total --}}
                    <div class="mt-6 flex justify-between font-bold text-lg border-t pt-4">
                        <span>Total:</span>
                        <span>
                            UGX {{
                                number_format(
                                    collect($cart)->reduce(function ($carry, $qty, $pid) use ($cartProducts) {
                                        $product = $cartProducts->get($pid);
                                        return $carry + ($product ? $product->price * $qty : 0);
                                    }, 0)
                                )
                            }}
                        </span>
                    </div>

                    {{-- Confirm Order Button --}}
                    <div class="text-right mt-6">
                        <x-filament::button
                            wire:click="confirmOrder"
                            color="success"
                            icon="heroicon-o-check-circle"
                        >
                            Confirm Order
                        </x-filament::button>
                    </div>
                @else
                    <p class="text-sm text-gray-500">Your cart is empty.</p>
                @endif
            </div>
        </x-filament::modal>

        {{-- Confirm Order Modal --}}
        <x-filament::modal
            wire:model="showConfirmModal"
            heading="Confirm Your Order"
            max-width="md"
        >
            <p class="text-sm text-gray-700 mb-4">
                Are you sure you want to place this order?
            </p>

            <x-slot name="footer">
                <x-filament::button color="gray" wire:click="$set('showConfirmModal', false)">
                    Cancel
                </x-filament::button>
                <x-filament::button color="success" wire:click="placeOrder">
                    Place Order
                </x-filament::button>
            </x-slot>
        </x-filament::modal>

    </div>
</x-filament-panels::page>