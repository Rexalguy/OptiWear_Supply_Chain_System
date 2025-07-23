<?php

namespace App\Filament\Vendor\Pages;

use Carbon\Carbon;
use App\Models\Order;
use Filament\Pages\Page;
use App\Models\OrderItem;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Vendor\Widgets\ProductRadar;
use App\Filament\Vendor\Widgets\VendorStatsOverview;
use App\Filament\Vendor\Widgets\MonthlyOrdersLineChart;
use App\Filament\Vendor\Widgets\CatergoryDoughnutAndPieChart;

class AnalyticsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = "Business Analytics";
    protected static ?string $navigationGroup = 'Analytics';
    protected static string $view = 'filament.vendor.pages.analytics-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            VendorStatsOverview::class,
        ];
    }



    protected function getFooterWidgets(): array
    {
        return [
            MonthlyOrdersLineChart::class,
            CatergoryDoughnutAndPieChart::class,
            ProductRadar::class,
        ];
    }

}
