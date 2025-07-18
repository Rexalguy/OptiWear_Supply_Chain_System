<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;

class ProductList extends Component
{
    public $products;
    public $selectedProduct = false;
    public $clickedProduct = null;

    public function mount()
    {
        $this->products = Product::all();
    }

    public function openProductModal($id)
    {
        $this->clickedProduct = Product::find($id);
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
