<?php

namespace App\Filament\Pages;

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
            \App\Filament\Widgets\ManufacturerTotalDemandWidget::class,
            \App\Filament\Widgets\ManufacturerTopCategoryWidget::class,
            \App\Filament\Widgets\ManufacturerWeeklyForecastWidget::class,
            \App\Filament\Widgets\ManufacturerActivePredictionsWidget::class,
        ];
    }
    
    public function getHeaderWidgetsColumns(): int | string | array
    {
        return 4;
    }
}
