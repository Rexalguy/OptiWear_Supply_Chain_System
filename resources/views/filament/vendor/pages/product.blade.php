<x-filament-panels::page>
   <style>
       .product-card {
           position: relative;
           background-color: white;
           border-radius: 1rem;
           box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
           border: 1px solid #f3f4f6;
           overflow: hidden;
           transform: translateY(0);
           transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
       }
       
       .product-card:hover {
           box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
           transform: translateY(-0.5rem);
       }
       
       .modal-backdrop {
           position: fixed;
           top: 0;
           left: 0;
           right: 0;
           bottom: 0;
           display: flex;
           align-items: center;
           justify-content: center;
           z-index: 50;
           background: rgba(0, 0, 0, 0.8);
           backdrop-filter: blur(8px);
           padding: 1rem;
           animation: fadeIn 0.3s ease;
       }
       
       .modal-content {
           background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
           border-radius: 24px;
           box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
           width: 100%;
           max-width: 32rem;
           position: relative;
           overflow: hidden;
           animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
       }
       
       
       .modal-body {
           padding: 2rem;
           background-color: white;
       }
       
       @keyframes fadeIn {
           from { opacity: 0; }
           to { opacity: 1; }
       }
       
       @keyframes slideUp {
           from { 
               transform: translateY(60px) scale(0.9); 
               opacity: 0; 
           }
           to { 
               transform: translateY(0) scale(1); 
               opacity: 1; 
           }
       }
       
       @media (prefers-color-scheme: dark) {
           .product-card {
               background-color: #1e293b;
               border-color: #334155;
           }
           .modal-body {
               background-color: #1e293b;
           }
       }
       
       .dark .product-card {
           background-color: #1e293b;
           border-color: #334155;
       }
       
       .dark .modal-body {
           background-color: #1e293b;
       }
   </style>
   
   <div class="p-6 space-y-8">
       <div class="flex items-center justify-between my-4">
           <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">🛒 Products</h2>
           
           <a href="{{ url('/vendor/place-order') }}" title="View Cart" class="relative">
               <x-filament::button class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 border-0 shadow-lg transform hover:scale-105 transition-all duration-200" size="lg" icon="heroicon-o-shopping-cart">
                   <span class="text-lg font-bold text-white">{{ $cartCount }}</span>
               </x-filament::button>
           </a>
       </div>

       <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
           @foreach ($products as $product)
           <div class="product-card group">
               <!-- Image Container -->
               <div wire:click="openProductModal({{ $product->id }})" class="relative h-48 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-600 cursor-pointer overflow-hidden">
                   <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/image.png' }}" 
                        alt="{{ $product->name }}" 
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                   <div class="absolute inset-0 bg-dark bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
               </div>
               
               <!-- Content -->
               <div class="p-6 space-y-4">
                   <div class="space-y-2">
                       <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">{{ $product->name }}</h3>
                       <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">SKU: {{ $product->sku }}</p>
                       <p class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">UGX {{ number_format($product->price) }}</p>
                   </div>
                   
                   <select wire:model="bale_sizes.{{ $product->id }}" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-slate-600 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 font-medium" style="color: black;">
                       <option value="">Select Bale Size</option>
                       <option value="100">Starter: 100 pieces</option>
                       <option value="350">Classic: 350 pieces</option>
                       <option value="750">Premium: 750 pieces</option>
                   </select>
                   
                   <x-filament::button 
                       wire:click="addToCart({{ $product->id }})"
                       class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border-0 shadow-lg transform hover:scale-105 transition-all duration-200"
                       size="lg"
                       icon="heroicon-m-plus"
                       wire:loading.attr="disabled"
                   >
                       <span wire:loading.remove wire:target="addToCart({{ $product->id }})">Add to Cart</span>
                       <span wire:loading wire:target="addToCart({{ $product->id }})">Adding...</span>
                   </x-filament::button>
               </div>
           </div>
           @endforeach
       </div>

       @if ($selectedProduct && $clickedProduct)
           <div class="modal-backdrop" wire:click.self="closeProductModal">
               <div class="modal-content" @click.stop>
                   <div class=""></div>
                   
                   <div class="modal-body">
                       <!-- Image -->
                       <div class="w-full h-64 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-600 rounded-2xl mb-6 overflow-hidden">
                           <img src="{{ $clickedProduct->image ? asset('storage/' . $clickedProduct->image) : '/images/image.png' }}" 
                                alt="{{ $clickedProduct->name }}" 
                                class="w-full h-full object-cover">
                       </div>
                       
                       <!-- Content -->
                       <div class="space-y-4">
                           <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $clickedProduct->name }}</h3>
                           <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">SKU: {{ $clickedProduct->sku }}</p>
                           <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">UGX {{ number_format($clickedProduct->price) }}</p>
                           <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $clickedProduct->description }}</p>
                           
                           <select wire:model="bale_sizes.{{ $clickedProduct->id }}" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-slate-600 text-gray-900 dark:text-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 font-medium bg-dark" style="margin-bottom: 20px; color:black;">
                               <option value="">Select Bale Size</option>
                               <option value="100">Starter: 100 pieces</option>
                               <option value="350">Classic: 350 pieces</option>
                               <option value="750">Premium: 750 pieces</option>
                           </select>
                       </div>
                       
                       <!-- Actions -->
                       <div class="flex gap-4 mt-8">
                           <x-filament::button 
                               wire:click="closeProductModal" 
                               class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 border-0 shadow-md"
                               size="lg"
                           >
                               Close
                           </x-filament::button>

                           <x-filament::button
                               wire:click="addToCart({{ $clickedProduct->id }})"
                               class="flex-1 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 border-0 shadow-lg transform hover:scale-105 transition-all duration-200"
                               size="lg"
                               icon="heroicon-m-plus"
                               wire:loading.attr="disabled"
                           >
                               <span wire:loading.remove wire:target="addToCart({{ $clickedProduct->id }})">Add to Cart</span>
                               <span wire:loading wire:target="addToCart({{ $clickedProduct->id }})">Adding...</span>
                           </x-filament::button>
                       </div>
                   </div>
               </div>
           </div>
       @endif
   </div>
</x-filament-panels::page>