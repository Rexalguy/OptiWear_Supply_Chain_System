<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Notifications\Notification;

class PlaceOrder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = "View Cart";
    protected static ?string $navigationGroup = 'Orders';
    protected static string $view = 'filament.vendor.pages.place-order';

    use Forms\Concerns\InteractsWithForms;
    public $cart;
    public $cartCount;
    public function mount()
    {
        $this->cart = session()->get('cart', []);
        $this->cartCount = session()->get('cartCount', 0);
    }
    public function reduceQuantity($id, $quantity)
    {
        if (isset($this->cart[$id])) {
            if ($this->cart[$id]['quantity'] > $quantity) {
                $this->cart[$id]['quantity'] -= $quantity;
            } else {
                unset($this->cart[$id]);
            }
            session()->put('cart', $this->cart);
            session()->put('cartCount', count($this->cart-quantity));
            $this->notify('success', 'Quantity reduced successfully.');
        } else {
            $this->notify('error', 'Product not found in cart.');
        }
    }
}