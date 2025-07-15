<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Models\Product as ProductModel;
use Illuminate\Contracts\Support\Htmlable;

class Product extends Page

{
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
    }
    public function openProductModal($productId)
    {
        $this->clickedProduct = ProductModel::find($productId);
        $this->selectedProduct = true;
    }
    public function closeProductModal()
    {
        $this->selectedProduct = false;
        $this->clickedProduct = null;
    }
}