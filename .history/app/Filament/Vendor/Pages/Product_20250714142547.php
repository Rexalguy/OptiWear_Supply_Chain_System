<?php

namespace App\Filament\Vendor\Pages;

use App\Models\Product as ProductModel;
use Filament\Pages\Page;

class Product extends Page

{
    public $products;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = "Shop Products";
    protected static ?string $navigationGroup = 'Products';

    protected static string $view = 'filament.vendor.pages.product';

    public function mount()
    {
        $this->products = ProductModel::all();
    }
}
