<x-filament-panels::page>
<div class="space-y-6 p-6">

    <h2 class="text-2xl font-bold">Available Products</h2>

    <ul class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($products as $product)
            <li class="border rounded p-4 shadow">
                <div class="font-semibold">{{ $product->name }}</div>
                <div>Price: ${{ number_format($product->price, 2) }}</div>
                <div>Available: {{ $product->quantity_available }}</div>

                <button
                    wire:click="addToCart({{ $product->id }})"
                    class="mt-2 px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Add to Cart
                </button>
            </li>
        @endforeach
    </ul>

    <hr>

    <h3 class="text-xl font-semibold">Your Cart</h3>

    @if (count($cart) > 0)
        <table class="w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 p-2">Product</th>
                    <th class="border border-gray-300 p-2">Quantity</th>
                    <th class="border border-gray-300 p-2">Unit Price</th>
                    <th class="border border-gray-300 p-2">Total Price</th>
                    <th class="border border-gray-300 p-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cart as $productId => $quantity)
                    @php
                        $product = $products->find($productId);
                    @endphp
                    @if ($product)
                        <tr>
                            <td class="border border-gray-300 p-2">{{ $product->name }}</td>
                            <td class="border border-gray-300 p-2">
                                <input
                                    type="number"
                                    min="1"
                                    max="{{ $product->quantity_available }}"
                                    value="{{ $quantity }}"
                                    wire:change="updateQuantity({{ $product->id }}, $event.target.value)"
                                    class="w-16 border rounded p-1"
                                />
                            </td>
                            <td class="border border-gray-300 p-2">${{ number_format($product->price, 2) }}</td>
                            <td class="border border-gray-300 p-2">${{ number_format($product->price * $quantity, 2) }}</td>
                            <td class="border border-gray-300 p-2">
                                <button
                                    wire:click="removeFromCart({{ $product->id }})"
                                    class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700"
                                >
                                    Remove
                                </button>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 text-right font-bold">
            Total: $
            {{
                number_format(
                    collect($cart)->reduce(function ($carry, $qty, $pid) use ($products) {
                        $product = $products->find($pid);
                        return $carry + ($product ? $product->price * $qty : 0);
                    }, 0),
                2)
            }}
        </div>

        <div class="mt-6 text-right">
            <a href="{{ route('customer.checkout') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Proceed to Checkout
            </a>
        </div>

    @else
        <p>Your cart is empty.</p>
    @endif

</div>
</x-filament-panels::page>
