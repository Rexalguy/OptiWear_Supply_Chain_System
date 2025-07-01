<x-filament-panels::page>
    <div class="p-6 space-y-6">
        <h2 class="text-2xl font-bold">Checkout</h2>

        @if (count($cart) > 0)
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="p-2 border">Product</th>
                        <th class="p-2 border">Quantity</th>
                        <th class="p-2 border">Unit Price</th>
                        <th class="p-2 border">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cart as $productId => $quantity)
                        @php
                            $product = $products->find($productId);
                        @endphp
                        @if ($product)
                            <tr>
                                <td class="p-2 border">{{ $product->name }}</td>
                                <td class="p-2 border">{{ $quantity }}</td>
                                <td class="p-2 border">${{ number_format($product->price, 2) }}</td>
                                <td class="p-2 border">${{ number_format($product->price * $quantity, 2) }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>

            <div class="text-right mt-4 font-bold text-lg">
                Total:
                ${{ number_format(
                    collect($cart)->reduce(function ($carry, $qty, $pid) use ($products) {
                        $product = $products->find($pid);
                        return $carry + ($product ? $product->price * $qty : 0);
                    }, 0), 2)
                }}
            </div>

            <div class="mt-6 text-right">
                <button  wire:click="placeOrder" types="button" 
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-md shadow-md">
                    Order
                </button>
            </div>
        @else
            <p>Your cart is empty.</p>
        @endif
    </div>
</x-filament-panels::page>