<x-filament-panels::page>
    <h1 class="text-2xl font-bold mb-4 flex justify-center">
        Checkout Page <span style="color: orange" class="px-2"> {{ $cartCount }} </span> Items in the Cart
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
       <div>
        <div class=>
            <x-filament::button color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">
                100
            </x-filament::button>
            <x-filament::button color="success" size="xs" icon="heroicon-m-plus" icon-position="before">
                100
            </x-filament::button>
        </div>
        <div>
            <x-filament::button color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">
                350
            </x-filament::button>
            <x-filament::button color="success" size="xs" icon="heroicon-m-plus" icon-position="before">
                350
            </x-filament::button>
        </div>
        <div>
            <x-filament::button color="danger" size="xs" icon="heroicon-m-minus" icon-position="before">
                750
            </x-filament::button>
            <x-filament::button color="success" size="xs" icon="heroicon-m-plus" icon-position="before">
                750
            </x-filament::button>
        </div>
       </div>
   @empty
      <div class="text-center text-gray-500 my-5 py-5">
          <p>Your cart is empty. Please add some products to proceed with checkout.</p>
      </div>
   @endforelse
</x-filament-panels::page>
