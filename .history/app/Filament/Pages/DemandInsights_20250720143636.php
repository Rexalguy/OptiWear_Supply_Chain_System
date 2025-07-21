<?php

namespace App\Filament\Pages;

use App\Filament\Manufacturer\Widgets\casualWearChart;
use App\Filament\Manufacturer\Widgets\childrenWearChart;
use App\Filament\Manufacturer\Widgets\formalWearChart;
use App\Filament\Manufacturer\Widgets\ManufacturerStatsOverview;
use App\Filament\Manufacturer\Widgets\percentageContributionChart;
use App\Filament\Manufacturer\Widgets\sportsWearChart;
use App\Filament\Manufacturer\Widgets\workWearChart;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;

class DemandInsights extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Demand Insights';
    protected static ?string $navigationGroup = 'Analytics';
    protected static string $view = 'filament.pages.demand-insights';

    protected function getActions(): array
    {
        return [
            Action::make('exportCharts')
                ->label('Export Charts')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->modalContent(view('filament.modals.export-options'))
                ->modalWidth(MaxWidth::TwoExtraLarge)
                ->modalHeading('Export Demand Insights Charts')
                ->modalDescription('Choose your export format and download your charts and data.')
                ->modalSubmitActionLabel('Start Export')
                ->modalCancelActionLabel('Cancel')
                ->action(function (array $data) {
                    // The actual export is handled by JavaScript
                    // This action is mainly for the modal
                    $this->js('executeExport()');
                })
                ->extraAttributes([
                    'onclick' => 'setTimeout(() => window.chartExporter?.detectCharts(), 100)'
                ])
        ];
    }

    protected function getHeaderWidgets(): array
{
    return [
        ManufacturerStatsOverview::class
    ];
}

    protected function getFooterWidgets(): array
{
    return [
        casualWearChart::class,
        childrenWearChart::class,
        formalWearChart::class,
        workWearChart::class,
        sportsWearChart::class,
        percentageContributionChart::class,
    ];
}
}
