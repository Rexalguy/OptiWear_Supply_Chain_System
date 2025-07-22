<x-filament-panels::page>
<div>
    <div class="flex justify-end">
        <a href="{{ url('/vendor/place-order') }}" title="View Cart">
            {{-- Cart Button --}}
            <x-filament::button color="" size="x-lg" weight="Semibold" icon="heroicon-o-shopping-cart">
                <H1 class="text-lg font-semibold p-2" ><span style="color: orange">{{ $cartCount }}</span> Items </H1>
            </x-filament::button>
        </a>
    </div>
    <div class="rounded border-gray-200 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
        @foreach ($products as $product)
        <div 
            class="rounded-xl  p-4 cursor-pointer shadow bg-white dark:bg-gray-800" 
            
        >
            <div wire:click="openProductModal({{ $product->id }})" class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="h-32 w-auto object-contain">
            </div>
            <div>
                <div>
                    <h3 class="text-lg font-semibold">{{ $product->name }}s</h3>
                    <p class="text-sm">SKU: {{ $product->sku }}</p>
                    <p class="text-sm">Price: UGX {{ number_format($product->unit_price) }}</p>
                    <select wire:model="bale_size.{{ $product->id }}"  class="form-select rounded-md shadow-sm my-2" style="background: #7293a7; color: #fffefe;">
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
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 modal-fade-in p-4 overflow-y-auto" style="cursor:pointer;" wire:click.self="closeProductModal">
            <div class="shadow bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-lg relative modal-slide-up my-8 mx-auto" style="cursor:auto;" @click.stop>
                <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-4 overflow-hidden flex-shrink-0">
                <img src="{{ asset($clickedProduct->image)}}" alt="{{ $clickedProduct->name }}" class="w-40 h-40 object-cover rounded">
                </div>
                <div>
                    <div>
                        <h3 class="text-lg font-semibold">{{ $clickedProduct->name }}</h3>
                        <p class="text-sm ">SKU: {{ $clickedProduct->sku }}</p>
                        <p class="text-sm ">Price: UGX {{ number_format($clickedProduct->unit_price) }}</p>
                        <p class="text-sm text-gray-600">{{ $clickedProduct->description }}</p>
                        <select wire:model="bale_size.{{ $clickedProduct->id }}"  class="form-select rounded-md shadow-sm my-2" style="background: #7293a7; color: #fffefe;">
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

                        <x-filament::button wire:click="addToCart({{ $product->id }})" color="warning" size="sm" icon="heroicon-m-plus" icon-position="after">
                        Add to Cart
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
<script>
    a
</script>

</x-filament-panels::page>