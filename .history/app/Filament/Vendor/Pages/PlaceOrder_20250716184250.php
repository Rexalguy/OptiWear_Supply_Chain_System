<?php

namespace App\Filament\Vendor\Pages;

use Filament\Forms;
use App\Models\Product;
use Filament\Pages\Page;
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
        self::getNavigationBadge();
        $this->cart = session()->get('cart', []);
        $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
    }
    public static function getNavigationBadge(): ?string
    {
        $cartCount = session()->get('cartCount', 0);
        return $cartCount > 0 ? (string) $cartCount : null;
    }
    public function notify(string $type, string $message): void
    {
        Notification::make()
            ->title($message)
            ->{$type}()
            ->send();
    }
    public function reduceQuantity($id, $quantity = 1)
    {
        if (isset($this->cart[$id])) {
            if ($this->cart[$id]['quantity'] > $quantity) {
                $this->cart[$id]['quantity'] -= $quantity;
            } else {
                unset($this->cart[$id]);
            }
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cart', $this->cart);
            session()->put('cartCount', $this->cartCount);
            $this->notify('success', 'Quantity reduced successfully.');
        } else {
            $this->notify('error', 'Product not found in cart.');
        }
        self::getNavigationBadge();
    }
    public function increaseQuantity($id, $quantity = 1)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity'] += $quantity;
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cart', $this->cart);
            session()->put('cartCount', array_sum(array_column($this->cart, 'quantity')));
            $this->notify('success', 'Quantity increased successfully.');
        } else {
            $this->notify('error', 'Product not found in cart.');
        }
        self::getNavigationBadge();
    }
    public function removeItem($id)
    {
        if (isset($this->cart[$id])) {
            unset($this->cart[$id]);
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cart', $this->cart);
            session()->put('cartCount', $this->cartCount);
            $this->notify('success', 'Item removed from cart.');
        } else {
            $this->notify('error', 'Product not found in cart.');
        }
        
    }
    public function placeOrder($id)
    {
        if (isset($this->cart[$id])) {
            $product = Product::find($id);
            if (!$product) {
                $this->notify('error', 'Product not found.');
                return;
            }

            //    Creating an order is left here am testing still

            unset($this->cart[$id]);
            session()->put('cart', $this->cart);
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cartCount', $this->cartCount);
            $this->notify('success', 'Order placed successfully.');
        } else {
            if (empty($this->cart)) {
                $this->notify('error', 'Your cart is empty.');
                return;
            }
        }
    }
}