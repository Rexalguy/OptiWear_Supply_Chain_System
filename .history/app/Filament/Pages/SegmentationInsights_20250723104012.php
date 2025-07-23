<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Manufacturer\Widgets\SegmentStatsWidget;
use App\Filament\Manufacturer\Widgets\SegmentationPolarChart;
use App\Filament\Manufacturer\Widgets\SegmentationBarChart;
use App\Filament\Manufacturer\Widgets\SegmentTopProductsTable;
use Filament\Actions\Action;
use Filament\Support\Enums\MaxWidth;

class SegmentationInsights extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Segmentation Insights';
    protected static ?string $navigationGroup = 'Analytics';
    protected static string $view = 'filament.pages.segmentation-insights';

    protected function getActions(): array
    {
        return [
            Action::make('populateInsights')
                ->label('Populate Insights')
                ->icon('heroicon-o-cpu-chip')
                ->color('secondary')
                ->requiresConfirmation()
                ->modalHeading('Generate Fresh Segmentation Insights')
                ->modalDescription('This will run the Python script to generate new customer segmentation data. The process may take a few minutes.')
                ->modalSubmitActionLabel('Generate Data')
                ->modalCancelActionLabel('Cancel')
                ->action(function () {
                    try {
                        // Show loading notification
                        \Filament\Notifications\Notification::make()
                            ->title('Processing...')
                            ->body('Generating segmentation insights data. Please wait...')
                            ->info()
                            ->persistent()
                            ->send();

                        // Execute the Artisan command
                        $exitCode = \Illuminate\Support\Facades\Artisan::call('insights:populate-segmentation');
                        
                        if ($exitCode === 0) {
                            // Success notification
                            \Filament\Notifications\Notification::make()
                                ->title('Success!')
                                ->body('Segmentation insights have been populated successfully. Charts will now show updated data.')
                                ->success()
                                ->duration(5000)
                                ->send();
                            
                            // Refresh the page to show new data
                            $this->js('window.location.reload()');
                        } else {
                            // Error notification
                            \Filament\Notifications\Notification::make()
                                ->title('Error')
                                ->body('Failed to populate segmentation insights. Please check the logs.')
                                ->danger()
                                ->persistent()
                                ->send();
                        }
                        
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Error')
                            ->body('An error occurred: ' . $e->getMessage())
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
            Action::make('exportCharts')
                ->label('Export Charts')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->modalContent(view('filament.modals.export-options'))
                ->modalWidth(MaxWidth::TwoExtraLarge)
                ->modalHeading('Export Segmentation Insights Charts')
                ->modalDescription('Download all segmentation charts on this page as high-quality PNG images.')
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
            SegmentStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            SegmentationPolarChart::class,
            SegmentationBarChart::class,
            SegmentTopProductsTable::class,
        ];
    }
}
