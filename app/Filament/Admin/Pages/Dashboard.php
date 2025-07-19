<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Filament\Admin\Widgets\AdminSalesChart;
use App\Filament\Admin\Widgets\AdminTopSalesBar;
use App\Filament\Admin\Widgets\AdminSalesPieChart;
use App\Filament\Admin\Widgets\AdminStatsOverview;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.admin.pages.dashboard';

        protected function getHeaderWidgets(): array
{
    return [
                AdminStatsOverview::class,
                AdminSalesPieChart::class,
                AdminSalesChart::class,
                AdminTopSalesBar::class,   
                
        
        
    ];
}
}
