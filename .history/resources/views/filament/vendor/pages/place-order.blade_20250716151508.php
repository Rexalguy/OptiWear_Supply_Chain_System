<x-filament-panels::page>
    <h1 class="text-2xl font-bold mb-4 flex justify-center">
        Checkout Page <span style="color: orange" class="px-2"> {{ $cartCount }} </span> Items in the Cart
    </h1>
   @forelse ($cart as $item)
       
   @empty
      <div class="text-center text-gray-500 my-5 py-5">
          <p>Your cart is empty. Please add some products to proceed with checkout.</p>
      </div>
   @endforelse
</x-filament-panels::page>
