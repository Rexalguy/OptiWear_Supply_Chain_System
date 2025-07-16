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
        $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
    }
    public function reduceQuantity($id, $quantity = 1)
    {
        if (isset($this->cart[$id])) {
            if ($this->cart[$id]['quantity'] > $quantity) {
                $this->cart[$id]['quantity'] -= $quantity;
            } else {
                unset($this->cart[$id]);
            }
            session()->put('cart', $this->cart);
            session()->put('cartCount', array_sum(array_column($this->cart, 'quantity')));
            $this->notify('success', 'Quantity reduced successfully.');
        } else {
            $this->notify('error', 'Product not found in cart.');
        }
    }
    public function increaseQuantity($id, $quantity = 1)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity'] += $quantity;
            session()->put('cart', $this->cart);
            session()->put('cartCount', array_sum(array_column($this->cart, 'quantity')));
            $this->notify('success', 'Quantity increased successfully.');
        } else {
            $this->notify('error', 'Product not found in cart.');
        }
    }
}