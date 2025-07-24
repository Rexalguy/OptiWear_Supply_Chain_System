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
    <div class="flex justify-end mb-6">
        <a href="{{ url('/vendor/place-order') }}" title="View Cart" class="group">
            {{-- Enhanced Cart Button --}}
            <div class="relative bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 rounded-2xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 p-1">
                <div class="bg-white dark:bg-gray-800 rounded-xl px-6 py-4 flex items-center space-x-3">
                    <div class="relative rounded-2xl">
                        <svg class="w-8 h-8 text-blue-600 dark:text-blue-400 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m2.6 8L6 5H3m4 8v6a2 2 0 002 2h8a2 2 0 002-2v-6m-6 6V9a2 2 0 00-2-2H9a2 2 0 00-2 2v8.01"></path>
                        </svg>
                        @if($cartCount > 0)
                            <div class="absolute -top-2 -right-2 bg-gradient-to-r from-orange-500 to-red-500 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center animate-pulse">
                                {{ $cartCount }}
                            </div>
                        @endif
                    </div>
                    <div class="text-left">
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">
                            Shopping Cart
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-semibold text-orange-600 dark:text-orange-400">{{ $cartCount }}</span> Items
                        </p>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="rounded border-gray-200 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-6">
        @foreach ($products as $product)
        <div 
            class="group relative rounded-[2rem] p-0 cursor-pointer shadow-lg hover:shadow-2xl bg-white dark:bg-gray-800 transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 overflow-hidden" 
        >
            <!-- Subtle gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 to-purple-50/30 dark:from-gray-800/50 dark:to-gray-900/50 rounded-[2rem]"></div>
            
            <!-- Product image with enhanced styling -->
            <div wire:click="openProductModal({{ $product->id }})" class="relative z-10 w-full h-64 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-t-[2rem] mb-4 overflow-hidden group-hover:scale-105 transition-transform duration-300 pt-4">
                <div class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-t-[2rem]"></div>
                <div class="relative z-10 w-56 h-56 rounded-[1.5rem] overflow-hidden drop-shadow-lg group-hover:scale-110 transition-transform duration-300 bg-white border-2 border-white/30">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-56 h-56 object-cover object-center rounded-[1.5rem]" style="width: 224px !important; height: 224px !important;">
                </div>
                <!-- Hover effect overlay -->
                <div class="absolute inset-0 bg-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-t-[2rem]"></div>
            </div>
            
            <div class="relative z-10 px-4 pb-4">
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{{ $product->name }}s</h3>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">SKU: <span class="text-gray-800 dark:text-gray-200">{{ $product->sku }}</span></p>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400">UGX {{ number_format($product->unit_price) }}</p>
                    </div>
                    
                    <!-- Enhanced select dropdown -->
                    <select wire:model="bale_size.{{ $product->id }}" class="w-full mt-3 px-4 py-3 rounded-[1rem] border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 transition-all duration-300 font-medium">
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
                        class="w-full justify-center font-semibold py-3 px-6 rounded-[1rem] bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
                    >
                        Add to Cart
                    </x-filament::button>
                </div>
            </div>
            
            <!-- Decorative corner accent -->
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-bl-[2rem]"></div>
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
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black/60 backdrop-blur-sm modal-fade-in p-6 overflow-y-auto" style="cursor:pointer;" wire:click.self="closeProductModal">
            <div class="relative shadow-2xl bg-white dark:bg-gray-800 rounded-[2rem] p-6 w-full max-w-4xl modal-slide-up my-8 mx-auto border border-gray-200 dark:border-gray-700" style="cursor:auto;" @click.stop>
                <!-- Gradient background overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-purple-50/50 dark:from-gray-800/50 dark:to-gray-900/50 rounded-[2rem]"></div>
                
                <!-- Enhanced close button -->
                <button wire:click="closeProductModal" class="absolute top-4 right-4 z-20 w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-700 hover:bg-red-100 dark:hover:bg-red-900/50 flex items-center justify-center close-btn-animate group">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400 group-hover:text-red-600 dark:group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Modal Content Grid Layout -->
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                    <!-- Product image section -->
                    <div class="w-full h-80 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-[1.5rem] overflow-hidden">
                        <div class="absolute inset-0 bg-white/90 dark:bg-gray-800/90 backdrop-blur-sm rounded-[1.5rem]"></div>
                        <div class="relative z-10 w-72 h-72 rounded-[1.2rem] overflow-hidden drop-shadow-xl border-2 border-white/30 bg-white">
                            <img src="{{ asset($clickedProduct->image)}}" alt="{{ $clickedProduct->name }}" class="w-72 h-72 object-cover object-center rounded-[1.2rem]" style="width: 288px !important; height: 288px !important;">
                        </div>
                        <!-- Decorative elements -->
                        <div class="absolute top-3 left-3 w-12 h-12 bg-blue-500/10 rounded-full"></div>
                        <div class="absolute bottom-3 right-3 w-8 h-8 bg-purple-500/10 rounded-full"></div>
                    </div>
                
                    <!-- Product details section -->
                    <div class="space-y-5">
                        <div>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">{{ $clickedProduct->name }}s</h3>
                            <div class="inline-flex items-center space-x-3 text-sm bg-gray-100 dark:bg-gray-700 rounded-full px-5 py-2">
                                <span class="text-gray-600 dark:text-gray-400">SKU:</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $clickedProduct->sku }}</span>
                            </div>
                        </div>
                        
                        <div>
                            <div class="text-4xl font-bold text-green-600 dark:text-green-400 mb-3">
                                UGX {{ number_format($clickedProduct->unit_price) }}
                            </div>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed text-base">{{ $clickedProduct->description }}</p>
                        </div>
                        
                        <!-- Enhanced package selection -->
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-[1rem] p-5">
                            <label class="block text-base font-semibold text-gray-700 dark:text-gray-300 mb-4">Choose Your Package:</label>
                            <select wire:model="bale_size.{{ $clickedProduct->id }}" class="w-full px-4 py-4 rounded-[1rem] border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 transition-all duration-300 font-medium text-base">
                                <option value="" class="text-gray-500">🎯 Select Package Size</option>
                                <option value="100" class="font-medium">🚀 Starter Package: 100 pieces</option>
                                <option value="350" class="font-medium">⭐ Classic Package: 350 pieces</option>
                                <option value="750" class="font-medium">💎 Premium Package: 750 pieces</option>
                            </select>
                        </div>
                        
                        <!-- Enhanced action buttons -->
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <x-filament::button 
                                wire:click="closeProductModal" 
                                color="gray" 
                                size="lg"
                                class="justify-center py-4 px-6 rounded-[1rem] font-semibold transition-all duration-300 hover:scale-105 text-base"
                            >
                                Close
                            </x-filament::button>

                            <x-filament::button 
                                wire:click="addToCart({{ $clickedProduct->id }})" 
                                color="warning" 
                                size="lg" 
                                icon="heroicon-m-plus" 
                                icon-position="after"
                                class="justify-center py-4 px-6 rounded-[1rem] bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white shadow-lg hover:shadow-xl font-semibold transition-all duration-300 hover:scale-105 text-base"
                            >
                                Add to Cart
                            </x-filament::button>
                        </div>
                    </div>
                </div>
                
                <!-- Decorative corner accents -->
                <div class="absolute top-0 left-0 w-16 h-16 bg-gradient-to-br from-blue-500/10 to-transparent rounded-br-[1.5rem]"></div>
                <div class="absolute bottom-0 right-0 w-16 h-16 bg-gradient-to-tl from-purple-500/10 to-transparent rounded-tl-[1.5rem]"></div>
            </div>
        </div>
    @endif
</div>

</x-filament-panels::page>