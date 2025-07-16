<x-filament-panels::page>
   @forelse (session('cart', []) as $item)
       <div>
           <h4>{{ $item['name'] }}</h4>
           <p>Price: {{ $item['price'] }}</p>
           <p>Quantity: {{ $item['quantity'] }}</p>
       </div>
   @empty
       
   @endforelse
</x-filament-panels::page>
