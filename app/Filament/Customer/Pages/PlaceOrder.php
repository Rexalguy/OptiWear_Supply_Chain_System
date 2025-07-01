<?php

namespace App\Filament\Customer\Pages;

use Filament\Pages\Page;
use App\Models\Product;

class PlaceOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static string $view = 'filament.customer.pages.place-order';

    public $products;
    public $cart = [];

    // Load products and cart on mount
    public function mount()
    {
        // Load products with available quantity > 0
        $this->products = Product::where('quantity_available', '>', 0)->get();

        // Load cart from session or initialize empty array
        $this->cart = session()->get('cart', []);
    }

    // Add product to cart with quantity increment
    public function addToCart($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]++;
        } else {
            $this->cart[$productId] = 1;
        }
        session()->put('cart', $this->cart);

        $this->dispatchBrowserEvent('notify', ['message' => 'Added to cart']);
    }

    // Remove product from cart
    public function removeFromCart($productId)
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
            session()->put('cart', $this->cart);
            $this->dispatchBrowserEvent('notify', ['message' => 'Removed from cart']);
        }
    }

    // Update quantity in cart
    public function updateQuantity($productId, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($productId);
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId] = $quantity;
            session()->put('cart', $this->cart);
            $this->dispatchBrowserEvent('notify', ['message' => 'Quantity updated']);
        }
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view(static::$view, [
            'products' => $this->products,
            'cart' => $this->cart,
        ]);
    }
}