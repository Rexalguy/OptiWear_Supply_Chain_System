<?php

namespace App\Filament\Vendor\Pages;

use App\Models\Product as ProductModel;
use Filament\Pages\Page;

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

    public function mount()
    {
        $this->products = ProductModel::all();
    }
    public function openProductModal($productId)
    {
        $this->clickedProduct = Product::find($productId);
        $this->selectedProduct = true;
    }
    public function closeProductModal()
    {
        $this->selectedProduct = false;
        $this->clickedProduct = null;
    }
}
