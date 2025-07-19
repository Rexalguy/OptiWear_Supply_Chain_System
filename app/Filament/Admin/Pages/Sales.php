<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\BubbleSalesChart;
use App\Filament\Admin\Widgets\LineSalesChart;
use App\Filament\Admin\Widgets\SalesStatsOverview;
use Filament\Pages\Page;

class Sales extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Sales';

    protected static string $view = 'filament.admin.pages.sales';


        protected function getHeaderWidgets(): array
{
    return [
        SalesStatsOverview::class,
        LineSalesChart::class,
        BubbleSalesChart::class,
        
        
    ];
}

        protected function getWidgets(): array
{
    return [


    ];
}
}
