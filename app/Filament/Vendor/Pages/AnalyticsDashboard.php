<?php

namespace App\Filament\Vendor\Pages;

use App\Filament\Vendor\Widgets\VendorStatsOverview;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Htmlable;

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

}
