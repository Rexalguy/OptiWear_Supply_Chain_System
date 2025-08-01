<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Manufacturer\Widgets\SegmentStatsWidget;
use App\Filament\Manufacturer\Widgets\SegmentationPolarChart;
use App\Filament\Manufacturer\Widgets\SegmentationBarChart;
use App\Filament\Manufacturer\Widgets\SegmentTopProductsTable;
use App\Filament\Manufacturer\Widgets\SegmentationTable;
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
