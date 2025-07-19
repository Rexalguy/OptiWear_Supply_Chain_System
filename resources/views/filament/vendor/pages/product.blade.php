<x-filament-panels::page>
   <style>
       .product-card {
           position: relative;
           background: white;
           border-radius: 1rem;
           box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
           border: 1px solid #f3f4f6;
           overflow: hidden;
           transform: translateY(0);
           transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
       }
       
       .product-card:hover {
           box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
           transform: translateY(-0.5rem);
       }
       
       .cart-section {
           background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
           border-radius: 1rem;
           color: white;
           margin-bottom: 2rem;
       }
       
       .cart-item {
           background: rgba(255, 255, 255, 0.1);
           border-radius: 0.75rem;
           backdrop-filter: blur(10px);
           border: 1px solid rgba(255, 255, 255, 0.2);
       }
       
       @media (prefers-color-scheme: dark) {
           .product-card {
               background-color: #1e293b;
               border-color: #334155;
           }
       }
       
       .dark .product-card {
           background-color: #1e293b;
           border-color: #334155;
       }
   </style>
   
   <div class="p-6 space-y-8">
       <!-- Cart Section -->
       @if (!empty($cart))
           <div class="cart-section p-6">
               <div class="flex items-center justify-between mb-6">
                   <h2 class="text-2xl font-bold">🛒 Cart ({{ $this->cartCount }} items)</h2>
                   <div class="text-2xl font-bold">
                       Total: UGX {{ 'Total amount here' }}
                   </div>
               </div>
               
               <div class="grid gap-4 mb-6">
                   @foreach ($cart as $item)
                       <div class="cart-item p-4 flex items-center justify-between">
                           <div class="flex-1">
                               <h3 class="font-semibold text-lg">{{ $item['name'] }}</h3>
                               <p class="text-white text-opacity-80">{{ $item['quantity'] ?? 0 }} pieces × UGX {{ number_format($item['price'] ?? 0) }}</p>
                               <p class="text-xl font-bold">UGX {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0)) }}</p>
                           </div>
                           
                           <div class="flex items-center gap-3">
                               <input 
                                   type="number" 
                                   wire:change="updateQuantity({{ $item['id'] ?? 0 }}, $event.target.value)"
                                   value="{{ $item['quantity'] ?? 0 }}"
                                   min="100"
                                   class="w-20 px-2 py-1 border border-white border-opacity-30 rounded-lg text-center bg-white bg-opacity-20 text-white placeholder-white placeholder-opacity-70"
                               >
                               <x-filament::button 
                                   wire:click="removeFromCart({{ $item['id'] ?? 0 }})"
                                   color="danger"
                                   size="sm"
                                   icon="heroicon-m-trash"
                               >
                               </x-filament::button>
                           </div>
                       </div>
                   @endforeach
               </div>
               
               <!-- Order Form -->
               <div class="border-t border-white border-opacity-30 pt-6">
                   <div class="grid md:grid-cols-2 gap-4 mb-4">
                       {{ $this->form }}
                   </div>
                   
                   <x-filament::button 
                       wire:click="placeOrder"
                       class="w-full bg-white text-purple-600 hover:bg-gray-100 border-0 shadow-lg transform hover:scale-105 transition-all duration-200"
                       size="lg"
                       icon="heroicon-m-check-circle"
                   >
                       Place Order - UGX : {{ 'Total amount here' }}
                   </x-filament::button>
               </div>
           </div>
       @endif

       <!-- Header -->
       <div class="flex items-center justify-between" style="margin-bottom: 2rem;">
           <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent"> Products</h2>
           
           @if (empty($cart))
               <div class="text-lg font-semibold text-gray-500">
                   Cart: {{ $this->cartCount }} items
               </div>
           @endif
       </div>

       <!-- Products Grid -->
       <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
           @foreach ($this->products as $product)
           <div class="product-card group">
               <!-- Image Container -->
               <div wire:click="openProductModal({{ $product->id }})" class="relative h-48 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-slate-700 dark:to-slate-600 cursor-pointer overflow-hidden">
                   <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/image.png' }}" 
                        alt="{{ $product->name }}" 
                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                   <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-300"></div>
               </div>
               
               <!-- Content -->
               <div class="p-6 space-y-4">
                   <div class="space-y-2">
                       <h3 class="text-xl font-bold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">{{ $product->name }}</h3>
                       <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">SKU: {{ $product->sku }}</p>
                       <p class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">UGX {{ number_format($product->price) }}</p>
                   </div>
                   
                   <select wire:model.lazy="bale_sizes.{{ $product->id }}" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-slate-600  dark:bg-slate-700 text-gray-900 dark:text-black focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-200 font-medium" style="color: black;">
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
                   >
                       Add to Cart
                   </x-filament::button>
               </div>
           </div>
           @endforeach
           {{ $this->products->links() }}
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