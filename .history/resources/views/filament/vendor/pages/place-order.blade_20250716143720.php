<x-filament-panels::page>
   @forelse ( as $item)
       <div>
           <h4>{{ $item['name'] }}</h4>
           <p>Price: {{ $item['price'] }}</p>
           <p>Quantity: {{ $item['quantity'] }}</p>
       </div>
   @empty
       <p>Your cart is empty.</p>
   @endforelse
</x-filament-panels::page>
