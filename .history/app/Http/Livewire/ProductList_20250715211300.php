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
        $this->cartCount = count(session()->get('cart', []));
        $this->cart = session()->get('cart', []);
        $this->products = Product::all();
    }

    public function openProductModal($productId)
    {
        $this->clickedProduct = Product::find($productId);
        $this->selectedProduct = true;
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);
        if ($product) {
            $this->cart[] = $product;
            $this->cartCount++;
            session()->put('cart', $this->cart);
        }
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
