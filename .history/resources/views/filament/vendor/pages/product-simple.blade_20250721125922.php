<x-filament-panels::page>
<div>
    <h1>Simple Test Page</h1>
    <p>Cart Count: {{ $cartCount }}</p>
    <p>Number of Products: {{ count($products) }}</p>
    
    @if(count($products) > 0)
        <div>First Product: {{ $products[0]->name }}</div>
    @endif
</div>
</x-filament-panels::page>
