<x-filament-panels::page>
    <h1>
        Checkout Page {{ $cartCount }} Items in Cart
    </h1>
   @forelse ($cart as $item)
       <div>
        <div  class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : '/images/image.png' }}" alt="{{ $product->name }}" class="h-full w-auto object-contain">
            </div>
           <h4>{{ $item['name'] }}</h4>
           <p>Price: {{ $item['price'] }}</p>
           <p>Quantity: {{ $item['quantity'] }}</p>
       </div>
   @empty
       <p>Your cart is empty.</p>
   @endforelse
</x-filament-panels::page>
