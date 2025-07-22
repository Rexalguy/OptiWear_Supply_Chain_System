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
                <img src="{{ $product->image}}" alt="{{ $product->name }}" class="h-full w-auto object-contain">
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

       <!-- Simple Product Modal (No Complex Operations) -->
       @if ($selectedProduct && $clickedProduct)
           <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click.self="closeProductModal">
               <div class="bg-white dark:bg-gray-800 rounded-xl p-6 w-full max-w-lg">
                   <div class="h-48 bg-gray-100 dark:bg-gray-700 rounded-lg mb-4 flex items-center justify-center overflow-hidden">
                       <img src="{{ $clickedProduct->image ? asset('storage/' . $clickedProduct->image) : '/images/image.png' }}" 
                            alt="{{ $clickedProduct->name }}" 
                            class="h-full object-contain">
                   </div>
                   
                   <div class="space-y-3">
                       <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $clickedProduct->name }}</h3>
                       <p class="text-sm text-gray-500">SKU: {{ $clickedProduct->sku }}</p>
                       <p class="text-2xl font-bold text-green-600">UGX {{ number_format($clickedProduct->price) }}</p>
                       @if($clickedProduct->description)
                           <p class="text-gray-600 dark:text-gray-300">{{ $clickedProduct->description }}</p>
                       @endif
                       
                       <select wire:model.lazy="bale_sizes.{{ $clickedProduct->id }}" class="w-full px-3 py-2 border rounded-lg" style="color: black;">
                           <option value="">Select Bale Size</option>
                           <option value="100">Starter: 100 pieces</option>
                           <option value="350">Classic: 350 pieces</option>
                           <option value="750">Premium: 750 pieces</option>
                       </select>
                   </div>
                   
                   <div class="flex gap-3 mt-6">
                       <x-filament::button wire:click="closeProductModal" color="gray" class="flex-1">
                           Close
                       </x-filament::button>
                       <x-filament::button wire:click="addToCart({{ $clickedProduct->id }})" color="primary" class="flex-1">
                           Add to Cart
                       </x-filament::button>
                   </div>
               </div>
           </div>
       @endif
   </div>
</x-filament-panels::page>