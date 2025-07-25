<x-filament-panels::page>
    <style>
.small-toast {
    font-size: 14px !important;
    padding: 8px 12px !important;
    min-height: auto !important;
}
.small-toast .swal2-icon {
    width: 20px !important;
    height: 20px !important;
    margin: 0 8px 0 0 !important;
}
.small-toast .swal2-content {
    margin: 0 !important;
    padding: 0 !important;
}
.toast-success .swal2-timer-progress-bar {
    height: 3px !important;
    background: #10b981 !important;
}
.toast-warning .swal2-timer-progress-bar {
    height: 3px !important;
    background: #f59e0b !important;
}
.toast-error .swal2-timer-progress-bar,
.toast-danger .swal2-timer-progress-bar {
    height: 3px !important;
    background: #ef4444 !important;
}
.swal2-popup.swal2-toast {
    transform: none !important;
}
</style>
<div>
    <div class="flex justify-end">
        <a href="{{ url('/vendor/place-order') }}" title="View Cart">
            {{-- Cart Button --}}
            <x-filament::button color="" size="x-lg" weight="Semibold" icon="heroicon-o-shopping-cart">
                <H1 class="text-lg font-semibold p-2" ><span style="color: orange">{{ $cartCount }}</span> Items </H1>
            </x-filament::button>
        </a>
    </div>
    <div class="rounded border-gray-200 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-6">
        @foreach ($products as $product)
        <div 
            class="group relative rounded-2xl p-6 cursor-pointer shadow-lg hover:shadow-2xl bg-white dark:bg-gray-800 transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 overflow-hidden" 
        >
            <!-- Subtle gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 to-purple-50/30 dark:from-gray-800/50 dark:to-gray-900/50 rounded-2xl"></div>
            
            <!-- Product image with enhanced styling -->
            <div wire:click="openProductModal({{ $product->id }})" class="relative z-10 w-full h-48 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-xl mb-4 overflow-hidden group-hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm"></div>
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="relative z-10 h-40 w-auto object-contain drop-shadow-lg group-hover:scale-110 transition-transform duration-300">
                <!-- Hover effect overlay -->
                <div class="absolute inset-0 bg-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-xl"></div>
            </div>
            
            <div class="relative z-10">
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{{ $product->name }}s</h3>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">SKU: <span class="text-gray-800 dark:text-gray-200">{{ $product->sku }}</span></p>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400">UGX {{ number_format($product->unit_price) }}</p>
                    </div>
                    
                    <!-- Enhanced select dropdown -->
                    <select wire:model="bale_size.{{ $product->id }}" class="w-full mt-3 px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 transition-all duration-300 font-medium">
                        <option value="" class="text-gray-500">🎯 Select Package Size</option>
                        <option value="100" class="font-medium">🚀 Starter: 100 pieces</option>
                        <option value="350" class="font-medium">⭐ Classic: 350 pieces</option>
                        <option value="750" class="font-medium">💎 Premium: 750 pieces</option>
                    </select>
                </div>
                
                <!-- Enhanced button -->
                <div class="pt-2">
                    <x-filament::button 
                        wire:click="addToCart({{ $product->id }})" 
                        color="warning" 
                        size="sm" 
                        icon="heroicon-m-plus" 
                        icon-position="after"
                        class="w-full justify-center font-semibold py-3 px-6 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
                    >
                        Add to Cart
                    </x-filament::button>
                </div>
            </div>
            
            <!-- Decorative corner accent -->
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-bl-3xl"></div>
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
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 modal-fade-in p-4 overflow-y-auto pt-16" style="cursor:pointer;" wire:click.self="closeProductModal">
            <div class="shadow bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-lg relative modal-slide-up my-8 mx-auto" style="cursor:auto;" @click.stop>
                <div class="w-full h-36 flex items-center justify-center bg-gray-100 rounded-md mb-4 overflow-hidden flex-shrink-0 pt-4">
                <img src="{{ asset($clickedProduct->image)}}" alt="{{ $clickedProduct->name }}" class="max-w-full max-h-full object-contain rounded">
                </div>
                <div>
                    <div>
                        <h3 class="text-lg font-semibold">{{ $clickedProduct->name }}s</h3>
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

</x-filament-panels::page>