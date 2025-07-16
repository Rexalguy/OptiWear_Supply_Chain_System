<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductList extends Component
{
    public $cartCount;
    public $cart = [];
    public $products;
    public $clickedProduct = null;
    public $selectedProduct = false;

    public function mount()
    {
        $this->products = Product::all();
    }

    public function openProductModal($productId)
    {
        $this->clickedProduct = Product::find($productId);
        $this->selectedProduct = true;
    }

    public function closeProductModal()
    {
        $this->selectedProduct = false;
        $this->clickedProduct = null;
    }

    public function render()
    {
        return view('livewire.product-list');
    }
}