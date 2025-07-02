<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filament Product Table --}}
        {{ $this->table }}

        {{-- CART SUMMARY --}}
        <x-filament::section>
            <x-slot name="header">
                <h2 class="text-xl font-semibold">Your Cart</h2>
            </x-slot>

            @if (!empty($cart))
                <ul class="space-y-4">
                    @foreach ($cart as $productId => $qty)
                        @php $product = \App\Models\Product::find($productId); @endphp

                        @if ($product)
                            <li class="flex justify-between items-center gap-4 border-b pb-2">
                                <div>
                                    <div class="font-medium">{{ $product->name }}</div>
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
                <div class="mt-4 flex justify-between font-bold">
                    <span>Total:</span>
                    <span>
                        UGX {{
                            number_format(
                                collect($cart)->reduce(function ($carry, $qty, $pid) {
                                    $product = \App\Models\Product::find($pid);
                                    return $carry + ($product ? $product->price * $qty : 0);
                                }, 0)
                            )
                        }}
                    </span>
                </div>

                {{-- Confirm Order Button --}}
                <div class="mt-6 text-right">
                    <x-filament::button
                        wire:click="confirmOrder"
                        color="success"
                        icon="heroicon-o-check-circle"
                    >
                        Confirm Order
                    </x-filament::button>
                </div>
            @else
                <p class="text-gray-500">Your cart is empty.</p>
            @endif
        </x-filament::section>

        {{-- CONFIRMATION MODAL --}}
        <x-filament::modal
            wire:model="showConfirmModal"
            heading="Confirm Order"
            max-width="md"
        >
            <p class="text-sm text-gray-700 mb-4">Are you sure you want to place this order?</p>

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