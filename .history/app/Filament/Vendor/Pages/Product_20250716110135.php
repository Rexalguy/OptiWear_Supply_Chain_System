<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Models\Product as ProductModel;
use CartObject;
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
        $this->cartCount = session()->get('cartCount', 0);
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
        if (!$this->bale_size) {
            $this->notify('danger', 'Please Select  a Bale size before continuing');
            return;
        }
        $target_product = ProductModel::find($productId);
        if ($target_product) {
            new CartObject(
                $target_product->id,
                $target_product->name,
                $target_product->price,
                $this->bale_size
            );
            $this->cart[] = $target_product;
            $this->cartCount += $this->bale_size ?? 1; // Default bale size to 1 if not set
            session()->put('cart', $this->cart);
            session()->put('cartCount', $this->cartCount);
            $this->notify('success', 'Product added to cart successfully!');
        }
    }
    public function closeProductModal()
    {
        $this->selectedProduct = false;
        $this->clickedProduct = null;
    }
}