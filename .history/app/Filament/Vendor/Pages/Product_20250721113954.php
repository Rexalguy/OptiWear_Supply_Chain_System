<?php

namespace App\Filament\Vendor\Pages;

use StatsOverview;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Models\Product as ProductModel;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;

class Product extends Page

{
    public $cartCount = 0;
    public $bale_size;
    public $cart = [];
    public $clickedProduct;
    public $selectedProduct = false;
    public $products;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 1;
    public static function getNavigationSort(): ?int
    {
        return 1; // Lower = higher in the group
    }

    protected static ?string $navigationLabel = "Shop In Bulk";
    protected static ?string $navigationGroup = 'Products';

    protected static string $view = 'filament.vendor.pages.product';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
    public function getTitle(): string | Htmlable
    {
        return __('Bulk purchase Page');
    }
    //description
    public function getSubHeading(): string | Htmlable
    {
        return __('Purchase products in bulk at discounted rates.');
    }
    public function mount()
    {
        if (session()->has('cart') || session()->has('cartCount')) {
            $this->cart = session()->get('cart', []);
            $this->cartCount = session()->get('cartCount', 0);
        } else {
            $this->cart = [];
            $this->cartCount = 0;
        }
    }
    public function notify(string $type, string $message): void
    {
        Notification::make()
            ->title($message)
            ->{$type}()
            ->send();
    }
    public function openProductModal($productId)
    {
        $this->clickedProduct = ProductModel::find($productId);
        $this->selectedProduct = true;
    }
    public function addToCart($productId)
    {
        $baleSize = (int) $this->bale_size;
        if ($baleSize <= 0) {
            $this->notify('danger', 'Please select a valid Bale size before continuing');
            return;
        }
        $target_product = ProductModel::find($productId);
        if ($target_product) {
            $cartItem = [
                'id' => $target_product->id,
                'name' => $target_product->name,
                'price' => $target_product->price,
                'quantity' => $baleSize,
                'image' => $target_product->image,

            ];
            if (isset($this->cart[$target_product->id])) {
                $this->cart[$target_product->id]['quantity'] += $cartItem['quantity'];
                $this->notify('warning', 'Product already in cart. Only quantity has been updated.');
            } else {
                $this->cart[$target_product->id] = $cartItem;
                $this->notify('success', 'Product added to cart successfully.');
            }
            $this->cartCount = collect($this->cart)->sum('quantity');
            session()->put('cart', $this->cart);
            session()->put('cartCount', $this->cartCount);
        }
    }
    public function closeProductModal()
    {
        $this->selectedProduct = false;
        $this->clickedProduct = null;
    }

    // Sync cart data from frontend (localStorage) to Livewire backend
    public function syncCart($cart, $cartCount)
    {
        if (is_array($cart)) {
            $this->cart = $cart;
            session()->put('cart', $cart);
        }
        if (is_numeric($cartCount)) {
            $this->cartCount = (int) $cartCount;
            session()->put('cartCount', $this->cartCount);
        }
    }
}