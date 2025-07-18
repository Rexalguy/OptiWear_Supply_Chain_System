<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\LowStock;
use App\Filament\Admin\Widgets\ProductsBarsLine;
use App\Filament\Admin\Widgets\ProductsPolarChart;
use App\Filament\Admin\Widgets\ProductsStatsOverview;
use Filament\Pages\Page;

class ProductsInsight extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationGroup = 'Product Supervison';


    protected static string $view = 'filament.admin.pages.products-insight';

    protected function getHeaderWidgets(): array
{
    return [
        ProductsStatsOverview::class,
        
        
    ];
}


protected function getFooterWidgets(): array
{
    return [
        ProductsBarsLine::class,
        ProductsPolarChart::class,
        LowStock::class,
    ];
}


}
