<x-filament-panels::page>
    <h1 class="text-2xl font-bold mb-4 flex justify-center">
        Checkout Page <span style="color: orange"> {{ $cartCount  }} </span>  Items in the Cart
    </h1>
   @forelse ($cart as $item)
       <div>
           <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
               <img src="{{ $item['image'] ? asset('storage/' . $item['image']) : '/images/image.png' }}" alt="{{ $item['name'] }}" class="h-full w-auto object-contain">
           </div>
           <h4>{{ $item['name'] }}</h4>
           <p>Price: {{ $item['price'] }}</p>
           <p>Quantity: {{ $item['quantity'] }}</p>
       </div>
   @empty
       <x-filament::empty-state
           icon="heroicon-o-shopping-cart"
           title="Your cart is empty"
           description="Add some products to your cart to proceed with checkout."
           :actions="'You can browse products from the vendor page.'"
   @endforelse
</x-filament-panels::page>
