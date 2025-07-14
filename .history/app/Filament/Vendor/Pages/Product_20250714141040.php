<?php

namespace App\Filament\Vendor\Pages;

use Filament\Pages\Page;

class Product extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Products';

    protected static string $view = 'filament.vendor.pages.product';
}
