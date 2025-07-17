<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class DemandInsights extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Demand Insights';
    protected static ?string $navigationGroup = 'Analytics';
    protected static string $view = 'filament.pages.demand-insights';
}
