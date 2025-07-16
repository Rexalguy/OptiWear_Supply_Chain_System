<x-filament-panels::page>
   <script>
    console,log('Cart:', @json(session('cart', [])));
    console,log('Cart Count:', @json(session('cartCount', 0)));
   </script>
</x-filament-panels::page>
