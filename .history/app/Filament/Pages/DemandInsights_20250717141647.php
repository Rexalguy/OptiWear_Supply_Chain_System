<?php

namespace App\Filament\Pages;

use App\Filament\Manufacturer\Widgets\casualWearChart;
use App\Filament\Manufacturer\Widgets\ManufacturerStatsOverview;
use Filament\Pages\Page;

class DemandInsights extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Demand Insights';
    protected static ?string $navigationGroup = 'Analytics';
    protected static string $view = 'filament.pages.demand-insights';

    protected function getHeaderWidgets(): array
{
    return [
        ManufacturerStatsOverview::class
    ];
}

    protected function getFooterWidgets(): array
{
    return [
        casualWearChart::class
        casualWearChart::class
    ];
}
}
