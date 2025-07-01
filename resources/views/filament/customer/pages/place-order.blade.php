<x-filament-panels::page>
<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold mb-4">Place Order</h1>

    <h2 class="text-xl font-semibold mb-2">Available Products</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach ($products as $product)
            <div class="border rounded p-4 flex flex-col justify-between">
                <div>
                    <h3 class="font-semibold text-lg">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-600">Price: ${{ number_format($product->price, 2) }}</p>
                    <p class="text-sm text-gray-600">Stock: {{ $product->quantity_available }}</p>
                </div>
                <button
                    wire:click="addToCart({{ $product->id }})"
                    class="mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded"
                >
                    Add to Cart
                </button>
            </div>
        @endforeach
    </div>

    <h2 class="text-xl font-semibold mt-8 mb-2">Cart Summary</h2>

    @if (count($cart) === 0)
        <p>Your cart is empty.</p>
    @else
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 p-2 text-left">Product</th>
                    <th class="border border-gray-300 p-2 text-center">Quantity</th>
                    <th class="border border-gray-300 p-2 text-right">Price</th>
                    <th class="border border-gray-300 p-2 text-right">Total</th>
                    <th class="border border-gray-300 p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart as $productId => $quantity)
                    @php
                        $product = $products->find($productId);
                    @endphp
                    <tr>
                        <td class="border border-gray-300 p-2">{{ $product->name }}</td>
                        <td class="border border-gray-300 p-2 text-center">
                            <input
                                type="number"
                                min="1"
                                class="w-16 border rounded text-center"
                                wire:change="updateQuantity({{ $productId }}, $event.target.value)"
                                value="{{ $quantity }}"
                            />
                        </td>
                        <td class="border border-gray-300 p-2 text-right">${{ number_format($product->price, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-right">${{ number_format($product->price * $quantity, 2) }}</td>
                        <td class="border border-gray-300 p-2 text-center">
                            <button
                                wire:click="removeFromCart({{ $productId }})"
                                class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded"
                            >Remove</button>
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="text-right font-bold p-2">Total:</td>
                    <td class="text-right font-bold p-2">${{ number_format($total, 2) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <button
            wire:click="placeOrder"
            class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded"
        >
            Place Order
        </button>
    @endif
</div>
</x-filament-panels::page>
