<x-filament-panels::page>
    <div class="p-6 space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">🛍️ Available Products</h2>

        <div class="mt-6 text-right">
            @if ($potentialTokens > 0)
                <div class="text-sm text-green-600 dark:text-green-400 text-right mb-2">
                    🎁 You will earn <strong>{{ $potentialTokens }}</strong> token{{ $potentialTokens > 1 ? 's' : '' }} for this order!
                </div>
            @else
                <div class="text-sm text-green-600 dark:text-green-400 text-right mb-2">
                    Make a purchase above <strong>UGX 50,000</strong> to earn tokens 🎁
                </div>
            @endif

            <x-filament::button color="primary" tag="a" href="{{ url('/customer/my-orders') }}">
                View Cart ({{ $this->cartCount }})
            </x-filament::button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="p-4 shadow-sm bg-white dark:bg-gray-800 cursor-pointer" wire:click="openProductModal({{ $product->id }})">
                    <div class="mb-4 w-full h-40 flex items-center justify-center bg-gray-100 rounded-md overflow-hidden">
                        <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/image.png' }}" alt="{{ $product->name }}" class="h-full w-auto object-contain">
                    </div>
                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</p>
                    <p class="text-sm font-semibold mt-1">UGX {{ number_format($product->price) }}</p>
                    <p class="text-sm mt-1 {{ $product->quantity_available > 10 ? 'text-green-600' : 'text-yellow-600' }}">
                        {{ $product->quantity_available }} in stock
                    </p>
                </div>
            @endforeach
        </div>

        @if ($selectedProduct && $clickedProduct)
            <style>
                .modal-fade-in {
                    animation: modalFadeIn 0.3s ease;
                }
                @keyframes modalFadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                .modal-slide-up {
                    animation: modalSlideUp 0.4s cubic-bezier(.4,0,.2,1);
                }
                @keyframes modalSlideUp {
                    from { transform: translateY(40px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
                .close-btn-animate {
                    transition: transform 0.2s;
                }
                .close-btn-animate:hover {
                    transform: scale(1.3) rotate(90deg);
                }
            </style>
            <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 modal-fade-in" style="cursor:pointer;" wire:click.self="closeProductModal">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative modal-slide-up" style="cursor:auto;" @click.stop>
                    <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-4xl font-extrabold focus:outline-none close-btn-animate" style="right:1rem;top:1rem;" wire:click="closeProductModal" aria-label="Close">&times;</button>
                    <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-4 overflow-hidden">
                        <img src="{{ $clickedProduct->image ? asset('storage/' . $clickedProduct->image) : '/images/image.png' }}" alt="{{ $clickedProduct->name }}" class="h-full w-auto object-contain">
                    </div>
                    <h3 class="text-lg font-semibold">{{ $clickedProduct->name }}</h3>
                    <p class="text-sm text-gray-500">SKU: {{ $clickedProduct->sku }}</p>
                    <p class="text-sm text-gray-600">Price: UGX {{ number_format($clickedProduct->price) }}</p>
                    <p class="text-sm text-gray-600">Available: {{ $clickedProduct->quantity_available }}</p>
                    <p class="text-sm text-gray-600">{{ $clickedProduct->description }}</p>
                    <button class="mt-4 btn btn-primary" wire:click="closeProductModal">Close</button>
                </div>
            </div>
        @endif
        </div>
    </div>
</x-filament-panels::page>