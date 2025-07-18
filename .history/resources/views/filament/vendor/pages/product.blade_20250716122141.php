<x-filament-panels::page>
<div>
    <div class="flex justify-end">
        <a href="{{ url('/vendor/place-order') }}" title="View Cart">
            {{-- Cart Button --}}
            <x-filament::button color="" size="x-lg" weight="Semibold" icon="heroicon-o-shopping-cart">
                <H1 class="text-lg font-semibold p-2" style="color: orange">{{ $cartCount }}</H1>
            </x-filament::button>
        </a>
    </div>
    <div class="rounded border-gray-200 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
        @foreach ($products as $product)
        <div 
            class="rounded-xl  p-4 cursor-pointer shadow bg-white dark:bg-gray-800" 
            
        >
            <div wire:click="openProductModal({{ $product->id }})" class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/image.png' }}" alt="{{ $product->name }}" class="h-full w-auto object-contain">
            </div>
            <div>
                <div>
                    <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                    <p class="text-sm">SKU: {{ $product->sku }}</p>
                    <p class="text-sm">Price: UGX {{ number_format($product->price) }}</p>
                    <select wire:model="bale_size"  class="form-select rounded-md shadow-sm my-2" style="background: #119ae9; color: #fffefe;">
                        <option value="" >Select a Bale size</option>
                        <option value="100">Starter Package: 100 pieces</option>
                        <option value="350">Classic Package: 350 pieces</option>
                        <option value="750">Premium Package: 750 pieces</option>
                    </select>
                </div>
                <div>
                    <x-filament::button wire:click="addToCart({{ $product->id }})" color="warning" size="sm" icon="heroicon-m-plus" icon-position="after">
                        Add to Cart
                    </x-filament::button>
                </div>
            </div>
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
            <div class="shadow bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md relative modal-slide-up" style="cursor:auto;" @click.stop>
                <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600 text-4xl font-extrabold focus:outline-none close-btn-animate" style="right:1rem;top:1rem;" wire:click="closeProductModal" aria-label="Close">&times;</button>
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-4 overflow-hidden">
                <img src="{{ $clickedProduct->image ? asset('storage/' . $clickedProduct->image) : '/images/image.png' }}" alt="{{ $clickedProduct->name }}" class="h-full w-auto object-contain">
                </div>
                <div>
                    <div>
                        <h3 class="text-lg font-semibold">{{ $clickedProduct->name }}</h3>
                        <p class="text-sm ">SKU: {{ $clickedProduct->sku }}</p>
                        <p class="text-sm ">Price: UGX {{ number_format($clickedProduct->price) }}</p>
                        <p class="text-sm text-gray-600">{{ $clickedProduct->description }}</p>
                        <select wire:model="bale_size"  class="form-select rounded-md shadow-sm my-2" style="background: #119ae9; color: #fffefe;">
                        <option value="" >Select a Bale size</option>
                        <option value="100">Starter Package: 100 pieces</option>
                        <option value="350">Classic Package: 350 pieces</option>
                        <option value="750">Premium Package: 750 pieces</option>
                    </select>
                    </div>
                    <div class="flex gap-2">

                        <x-filament::button wire:click="closeProductModal" color="success" size="sm">
                        close
                        </x-filament::button>

                        <x-filament::button wire:click="addToCart" color="warning" size="sm" icon="heroicon-m-plus" icon-position="after">
                        Add to Cart
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

</x-filament-panels::page>
<script>
    sessionS
</script>