<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Models\Product as ProductModel;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Support\Htmlable;

class Product extends Page

{
    public $cartCount = 0;
    public $min_order_quantity = 150;
    public $cart = [];
    public $clickedProduct;
    public $selectedProduct = false;
    public $products;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 1;

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
        $this->products = ProductModel::all();
        $this->cart = session()->get('cart', []);
        $this->cartCount = count($this->cart);
        $this->min_order_quantity = 150;
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
        $product = ProductModel::find($productId);
        if ($product) {
            $this->cart[] = $product;
            $this->cartCount += $this->min_order_quantity;
            session()->put('cart', $this->cart);
            se
            $this->notify('success', 'Product added to cart successfully!');
        }
    }
    public function closeProductModal()
    {
        $this->selectedProduct = false;
        $this->clickedProduct = null;
    }
}