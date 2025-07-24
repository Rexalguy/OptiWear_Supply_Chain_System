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

/* Form select styles */
.select-container {
    width: 100%;
    position: relative;
    margin: 0.5rem 0;
}

.select-container select {
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
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
            class="group relative rounded-2xl p-0 cursor-pointer shadow-lg hover:shadow-2xl bg-white dark:bg-gray-800 transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 overflow-hidden" 
            style="border-radius: 1rem !important;"
        >
            <!-- Subtle gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/30 to-purple-50/30 dark:from-gray-800/50 dark:to-gray-900/50 rounded-2xl" style="border-radius: 1rem !important;"></div>
            
            <!-- Product image with enhanced styling -->
            <div wire:click="openProductModal({{ $product->id }})" class="relative z-10 w-full h-64 flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 rounded-t-2xl mb-4 overflow-hidden group-hover:scale-105 transition-transform duration-300 pt-4" style="border-top-left-radius: 1rem !important; border-top-right-radius: 1rem !important;">
                <div class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-t-2xl" style="border-top-left-radius: 1rem !important; border-top-right-radius: 1rem !important;"></div>
                <div class="relative z-10 w-56 h-56 rounded-xl overflow-hidden drop-shadow-lg group-hover:scale-110 transition-transform duration-300 bg-white border-2 border-white/30" style="border-radius: 0.75rem !important;">
                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-56 h-56 object-cover object-center rounded-xl" style="width: 224px !important; height: 224px !important; border-radius: 0.75rem !important;">
                </div>
                <!-- Hover effect overlay -->
                <div class="absolute inset-0 bg-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 rounded-t-2xl" style="border-top-left-radius: 1rem !important; border-top-right-radius: 1rem !important;"></div>
            </div>
            
            <div class="relative z-10 px-4 pb-4">
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors duration-300">{{ $product->name }}s</h3>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">SKU: <span class="text-gray-800 dark:text-gray-200">{{ $product->sku }}</span></p>
                        <p class="text-lg font-bold text-green-600 dark:text-green-400">UGX {{ number_format($product->unit_price) }}</p>
                    </div>
                    
                    <!-- Enhanced select dropdown -->
                    <select wire:model="bale_size.{{ $product->id }}" class="w-full mt-3 px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 transition-all duration-300 font-medium" style="border-radius: 0.5rem !important;">
                        <option value="" class="text-gray-500"> Select Package Size</option>
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
                        class="w-full justify-center font-semibold py-3 px-6 rounded-lg bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
                    >
                        Add to Cart
                    </x-filament::button>
                </div>
            </div>
            
            <!-- Decorative corner accent -->
            <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-500/10 to-purple-500/10 rounded-bl-xl"></div>
        </div>
        @endforeach
    </div>

    @if ($selectedProduct && $clickedProduct)
        <style>
            .modal-fade-in {
                animation: modalFadeIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            @keyframes modalFadeIn {
                from { 
                    opacity: 0; 
                    backdrop-filter: blur(0px);
                }
                to { 
                    opacity: 1; 
                    backdrop-filter: blur(12px);
                }
            }
            .modal-slide-up {
                animation: modalSlideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            }
            @keyframes modalSlideUp {
                from { 
                    transform: translateY(60px) scale(0.95); 
                    opacity: 0; 
                }
                to { 
                    transform: translateY(0) scale(1); 
                    opacity: 1; 
                }
            }
            .close-btn-animate {
                transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            }
            .close-btn-animate:hover {
                transform: scale(1.2) rotate(90deg);
                box-shadow: 0 10px 25px rgba(239, 68, 68, 0.3);
            }
            
            /* Premium glow effects */
            .premium-glow {
                position: relative;
            }
            .premium-glow::before {
                content: '';
                position: absolute;
                inset: -2px;
                background: linear-gradient(45deg, #3b82f6, #8b5cf6, #ec4899, #f59e0b);
                border-radius: inherit;
                opacity: 0;
                transition: opacity 0.3s;
                z-index: -1;
                filter: blur(8px);
            }
            .premium-glow:hover::before {
                opacity: 0.4;
            }
            
            /* Floating animation */
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-10px); }
            }
            .float-animation {
                animation: float 3s ease-in-out infinite;
            }
            
            /* Gradient text animation */
            @keyframes gradientShift {
                0% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
                100% { background-position: 0% 50%; }
            }
            .animated-gradient {
                background-size: 200% 200%;
                animation: gradientShift 3s ease infinite;
            }
            
            /* Shimmer effect */
            @keyframes shimmer {
                0% { transform: translateX(-100%); }
                100% { transform: translateX(100%); }
            }
            .shimmer::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
                transform: translateX(-100%);
                animation: shimmer 2s infinite;
            }
        </style>
        <div class="fixed inset-0 flex items-center justify-center z-50 bg-black/70 backdrop-blur-lg modal-fade-in p-6 overflow-y-auto" style="cursor:pointer;" wire:click.self="closeProductModal">
            <div class="relative shadow-2xl bg-white dark:bg-gray-900 rounded-xl p-0 w-full max-w-6xl modal-slide-up my-8 mx-auto overflow-hidden backdrop-blur-sm" style="cursor:auto; border-radius: 1rem !important;" @click.stop>
                <!-- Enhanced gradient background overlay -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50/80 via-purple-50/60 to-pink-50/80 dark:from-gray-800/90 dark:via-gray-900/80 dark:to-gray-800/90 rounded-xl" style="border-radius: 1rem !important;"></div>
                
                <!-- Premium close button -->
                <button wire:click="closeProductModal" class="absolute top-6 right-6 z-30 w-12 h-12 rounded-full bg-gradient-to-r from-red-500/10 to-pink-500/10 hover:from-red-500/20 hover:to-pink-500/20 backdrop-blur-sm border border-white/30 dark:border-gray-600/30 flex items-center justify-center close-btn-animate group transition-all duration-300">
                    <svg class="w-6 h-6 text-gray-700 dark:text-gray-300 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Premium flex layout -->
                <div class="relative z-10 flex gap-8 h-[650px]">
                    <!-- Enhanced image section -->
                    <div class="w-2/5 relative bg-gradient-to-br from-gray-100 via-blue-50 to-purple-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 rounded-l-xl flex items-center justify-center pr-2 pt-2 pb-2 overflow-hidden" style="border-top-left-radius: 1rem !important; border-bottom-left-radius: 1rem !important;">
                        <!-- Animated background -->
                        <div class="absolute inset-0 bg-gradient-to-br from-white/95 via-blue-50/90 to-purple-50/95 dark:from-gray-800/95 dark:via-gray-900/90 dark:to-gray-800/95 rounded-l-xl" style="border-top-left-radius: 1rem !important; border-bottom-left-radius: 1rem !important;"></div>
                        
                        <!-- Floating particles effect -->
                        <div class="absolute top-10 left-10 w-3 h-3 bg-blue-400/30 rounded-full animate-pulse"></div>
                        <div class="absolute top-32 left-16 w-2 h-2 bg-purple-400/30 rounded-full animate-ping"></div>
                        <div class="absolute bottom-20 left-8 w-4 h-4 bg-pink-400/20 rounded-full animate-pulse"></div>
                        
                        <!-- Main image container -->
                        <div class="relative z-10 w-full h-full flex items-center justify-center group">
                            <div class="relative w-[420px] h-[420px] rounded-xl overflow-hidden drop-shadow-2xl border-4 border-white/50 dark:border-gray-700/50 bg-white dark:bg-gray-800 transition-all duration-500 group-hover:scale-105 group-hover:rotate-1 premium-glow float-animation">
                                <!-- Image glow effect -->
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 via-purple-500/10 to-pink-500/10 rounded-xl"></div>
                                <img src="{{ asset($clickedProduct->image)}}" alt="{{ $clickedProduct->name }}" class="w-full h-full object-cover object-center rounded-lg transition-all duration-500 group-hover:brightness-110 shimmer" style="width: 420px !important; height: 420px !important;">
                                <!-- Overlay shine effect -->
                                <div class="absolute inset-0 bg-gradient-to-tr from-transparent via-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-lg"></div>
                            </div>
                        </div>
                        
                        <!-- Enhanced decorative elements -->
                        <div class="absolute top-6 left-6 w-16 h-16 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-full backdrop-blur-sm"></div>
                        <div class="absolute bottom-6 left-6 w-12 h-12 bg-gradient-to-br from-purple-500/20 to-pink-500/20 rounded-full backdrop-blur-sm"></div>
                        <div class="absolute top-1/2 left-4 w-8 h-8 bg-gradient-to-br from-pink-500/15 to-orange-500/15 rounded-full backdrop-blur-sm"></div>
                    </div>
                
                    <!-- Enhanced content section -->
                    <div class="flex-1 p-10 pr-20 space-y-8 flex flex-col justify-center bg-gradient-to-b from-white/50 to-gray-50/50 dark:from-gray-900/50 dark:to-gray-800/50 backdrop-blur-sm rounded-r-xl" style="border-top-right-radius: 1rem !important; border-bottom-right-radius: 1rem !important; padding-right: 5rem !important;">
                        <!-- Title section -->
                        <div class="space-y-4">
                            <h3 class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-gray-900 via-blue-800 to-purple-900 dark:from-white dark:via-blue-200 dark:to-purple-200 mb-4 leading-tight animated-gradient">{{ $clickedProduct->name }}s</h3>
                            <div class="inline-flex items-center space-x-4 text-base bg-gradient-to-r from-gray-100 to-blue-50 dark:from-gray-800 dark:to-gray-700 rounded-full px-6 py-3 border border-gray-200/50 dark:border-gray-600/50 backdrop-blur-sm premium-glow">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">SKU:</span>
                                <span class="font-bold text-gray-800 dark:text-gray-200">{{ $clickedProduct->sku }}</span>
                            </div>
                        </div>
                        
                        <!-- Price section -->
                        <div class="space-y-4">
                            <div class="text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 dark:from-green-400 dark:via-emerald-400 dark:to-teal-400 mb-4 leading-none animated-gradient">
                                UGX {{ number_format($clickedProduct->unit_price) }}
                            </div>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed text-xl font-medium max-w-md">{{ $clickedProduct->description }}</p>
                        </div>
                        
                        <!-- Premium package selection -->
                        <div class="bg-gradient-to-br from-white to-blue-50/50 dark:from-gray-800/80 dark:to-gray-700/80 rounded-xl p-8 backdrop-blur-sm shadow-xl premium-glow">
                            <label class="block text-xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center space-x-2">
                                <span>Choose Your Perfect Package</span>
                            </label>
                            <select wire:model="bale_size.{{ $clickedProduct->id }}" class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-800 transition-all duration-300 font-medium">
                                <option value="" class="text-gray-500"> Select Package Size</option>
                                <option value="100" class="font-medium">🚀 Starter: 100 pieces</option>
                                <option value="350" class="font-medium">⭐ Classic: 350 pieces</option>
                                <option value="750" class="font-medium">💎 Premium: 750 pieces</option>
                            </select>
                        </div>
                        
                        <!-- Premium action buttons -->
                        <div class="flex justify-between pt-6">
                            <x-filament::button 
                                wire:click="closeProductModal" 
                                color="gray" 
                                size="xl"
                                class="justify-center py-6 px-12 rounded-xl font-bold transition-all duration-300 hover:scale-105 text-xl bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 backdrop-blur-sm shadow-xl hover:shadow-2xl premium-glow"
                            >
                                Close
                            </x-filament::button>

                            <x-filament::button 
                                wire:click="addToCart({{ $clickedProduct->id }})" 
                                color="warning" 
                                size="xl" 
                                icon="heroicon-m-plus" 
                                icon-position="after"
                                class="justify-center py-6 px-12 rounded-xl bg-gradient-to-r from-orange-500 via-amber-500 to-yellow-500 hover:from-orange-600 hover:via-amber-600 hover:to-yellow-600 text-white shadow-xl hover:shadow-2xl font-bold transition-all duration-300 hover:scale-105 text-xl backdrop-blur-sm premium-glow shimmer"
                            >
                                Add to Cart
                            </x-filament::button>
                        </div>
                    </div>
                </div>
                
                <!-- Enhanced decorative corner accents -->
                <div class="absolute top-0 left-0 w-24 h-24 bg-gradient-to-br from-blue-500/20 via-purple-500/15 to-transparent rounded-br-xl backdrop-blur-sm"></div>
                <div class="absolute bottom-0 right-0 w-24 h-24 bg-gradient-to-tl from-purple-500/20 via-pink-500/15 to-transparent rounded-tl-xl backdrop-blur-sm"></div>
                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-bl from-pink-500/15 via-purple-500/10 to-transparent rounded-bl-xl backdrop-blur-sm"></div>
            </div>
        </div>
    @endif
</div>

</x-filament-panels::page>