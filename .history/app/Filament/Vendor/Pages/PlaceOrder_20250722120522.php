<?php

namespace App\Filament\Vendor\Pages;

use Filament\Forms;
use App\Models\Product;
use Filament\Pages\Page;

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

        // Calculate packages for each cart item
        foreach ($this->cart as $id => $item) {
            $this->cart[$id]['packages'] = $this->calculatePackages($id, $item['quantity']);
        }
    }
    // Removed duplicate declaration of $navigationSort
    public static function getNavigationSort(): ?int
    {
        return 2; // Lower = higher in the group
    }
    public static function getNavigationBadge(): ?string
    {
        $cartCount = session()->get('cartCount', 0);
        return (string) $cartCount;
    }
    public function calculatePackages($productId, $quantity)
    {
        $premiumCount = floor($quantity / 750);
        $remainder = $quantity % 750;
        $standardCount = floor($remainder / 350);
        $remainder = $remainder % 350;
        $starterCount = floor($remainder / 100); // Changed from 150 to 100 to match your starter package size

        return [
            'premium' => $premiumCount,
            'standard' => $standardCount,
            'starter' => $starterCount,
        ];
    }
    public function reduceQuantity($id, $quantity = 1)
    {
        if (isset($this->cart[$id])) {
            if ($this->cart[$id]['quantity'] > $quantity) {
                $this->cart[$id]['quantity'] -= $quantity;
                $this->cart[$id]['packages'] = $this->calculatePackages($id, $this->cart[$id]['quantity']);
            } else {
                unset($this->cart[$id]);
            }
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cart', $this->cart);
            session()->put('cartCount', $this->cartCount);
        }
        self::getNavigationBadge();
    }
    public function increaseQuantity($id, $quantity = 1)
    {
        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity'] += $quantity;
            $this->cart[$id]['packages'] = $this->calculatePackages($id, $this->cart[$id]['quantity']);
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cart', $this->cart);
            session()->put('cartCount', array_sum(array_column($this->cart, 'quantity')));
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
        }
        self::getNavigationBadge();
    }
    public function placeOrder($id)
    {
        if (isset($this->cart[$id])) {
            $product = Product::find($id);
            if (!$product) {
                return;
            }

            //    Creating an order is left here am testing still

            unset($this->cart[$id]);
            session()->put('cart', $this->cart);
            $this->cartCount = array_sum(array_column($this->cart, 'quantity'));
            session()->put('cartCount', $this->cartCount);
        } else {
            if (empty($this->cart)) {
                return;
            }
        }
        self::getNavigationBadge();
    }
}
