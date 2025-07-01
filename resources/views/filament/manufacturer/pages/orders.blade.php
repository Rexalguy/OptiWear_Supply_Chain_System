<x-filament-panels::page>
<div class="p-6 space-y-6">
    <h1 class="text-2xl font-bold mb-4">Manufacturer Orders</h1>

    @if ($orders->isEmpty())
        <p>No orders found.</p>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <div class="border rounded p-4 shadow-sm">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold">
                            Order #{{ $order->id }} — Status: <span class="capitalize">{{ $order->status }}</span>
                        </h2>
                        <span class="text-sm text-gray-600">
                            Placed on: {{ $order->created_at->format('M d, Y H:i') }}
                        </span>
                    </div>

                    <p class="mt-1 text-sm text-gray-700">Customer: {{ $order->creator->name ?? 'Unknown' }}</p>

                    <table class="w-full mt-3 table-auto border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border border-gray-300 p-2 text-left">Product</th>
                                <th class="border border-gray-300 p-2 text-center">Quantity</th>
                                <th class="border border-gray-300 p-2 text-right">Unit Price</th>
                                <th class="border border-gray-300 p-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="border border-gray-300 p-2">{{ $item->product->name ?? 'N/A' }}</td>
                                    <td class="border border-gray-300 p-2 text-center">{{ $item->quantity }}</td>
                                    <td class="border border-gray-300 p-2 text-right">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="border border-gray-300 p-2 text-right">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="3" class="text-right font-bold p-2">Order Total:</td>
                                <td class="text-right font-bold p-2">${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    {{-- Optional: Add buttons to update order status --}}
                    <div class="mt-3 flex space-x-2">
                        <button
                            wire:click="updateOrderStatus({{ $order->id }}, 'pending')"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded"
                        >
                            Pending
                        </button>
                        <button
                            wire:click="updateOrderStatus({{ $order->id }}, 'confirmed')"
                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded"
                        >
                            Confirmed
                        </button>
                        <button
                            wire:click="updateOrderStatus({{ $order->id }}, 'delivered')"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded"
                        >
                            Delivered
                        </button>
                        <button
                            wire:click="updateOrderStatus({{ $order->id }}, 'cancelled')"
                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded"
                        >
                            Cancelled
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

</x-filament-panels::page>
