<?php

use App\Models\Product;
use Livewire\Component;

class ProductComponent extends Component
{
    public $products;
    public $selectedProduct = false;

    public function mount()
    {
        $this->products = Product::all();
    }

    protected $listeners = ['openProductModal', 'closeProductModal'];

    public function openProductModal($id)
    {
        $this->selectedProduct = Product::find($id);
    }

    public function closeProductModal()
    {
        $this->selectedProduct = false;
    }

    public function render()
    {
        return view('livewire.product-component');
    }
}
?>
<x-filament-panels::page>
    <div>
        <div class="rounded border-gray-200 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
            @foreach ($products as $product)
            <div    class="rounded-xl shadow-sm p-4 cursor-pointer"
                    wire:click="openProductModal({{ $product->id }})"
                >

                <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-2 overflow-hidden">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-full w-auto object-contain">
                </div>
                <h3 class="text-lg font-semibold">{{ $product->name }}</h3>
                <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                <p class="text-sm text-gray-600">Price: UGX {{ number_format($product->price) }}</p>
                <p class="text-sm text-gray-600">Available: {{ $product->quantity_available }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <x-filament::modal id="productModal" :visible="$selectedProduct" wire:click="closeProductModal">
    @if ($clickedProduct)
        <div class="p-4">
            <div class="w-full h-40 flex items-center justify-center bg-gray-100 rounded-md mb-4 overflow-hidden">
                <img src="{{ asset('storage/' . $clickedProduct->image) }}" alt="{{ $clickedProduct->name }}" class="h-full w-auto object-contain">
            </div>
            <h3 class="text-lg font-semibold">{{ $clickedProduct->name }}</h3>
            <p class="text-sm text-gray-500">SKU: {{ $clickedProduct->sku }}</p>
            <p class="text-sm text-gray-600">Price: UGX {{ number_format($clickedProduct->price) }}</p>
            <p class="text-sm text-gray-600">Available: {{ $clickedProduct->quantity_available }}</p>
            <p class="text-sm text-gray-600">{{ $clickedProduct->description }}</p>
        </div>
    @endif
</x-filament::modal>

</x-filament-panels::page>
