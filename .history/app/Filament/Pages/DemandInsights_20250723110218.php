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
            Action::make('populateInsights')
                ->label('Populate Insights')
                ->icon('heroicon-o-cpu-chip')
                ->color('secondary')
                ->requiresConfirmation()
                ->modalHeading('Generate Fresh Demand Insights')
                ->modalDescription('This will run the Python script to generate new demand prediction data. The process may take a few minutes.')
                ->modalSubmitActionLabel('Generate Data')
                ->modalCancelActionLabel('Cancel')
                ->action(function () {
                    try {
                        // Execute the Artisan command silently
                        $exitCode = \Illuminate\Support\Facades\Artisan::call('insights:populate-demand');
                        
                        // Refresh the page to show new data after a short delay
                        if ($exitCode === 0) {
                            $this->js('setTimeout(() => window.location.reload(), 2000)');
                        }
                        
                    } catch (\Exception $e) {
                        // Silent execution - no notifications
                    }
                }),
            Action::make('exportCharts')
                ->label('Export Charts')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->modalContent(view('filament.modals.export-options'))
                ->modalWidth(MaxWidth::TwoExtraLarge)
                ->modalHeading('Export Demand Insights Charts')
                ->modalDescription('Download all charts on this page as high-quality PNG images.')
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
