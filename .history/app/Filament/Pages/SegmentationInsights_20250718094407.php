<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Manufacturer\Widgets\SegmentStatsWidget;
use App\Filament\Manufacturer\Widgets\SegmentDistributionChart;
use App\Filament\Manufacturer\Widgets\SegmentPurchaseBehaviorChart;
use App\Filament\Manufacturer\Widgets\AgeGroupPurchasePatternChart;
use App\Filament\Manufacturer\Widgets\SegmentRecommendationsTable;

class SegmentationInsights extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Segmentation Insights';
    protected static ?string $navigationGroup = 'Analytics';
    protected static string $view = 'filament.pages.segmentation-insights';

    protected function getHeaderWidgets(): array
    {
        return [
            SegmentStatsWidget::class,
            SegmentDistributionChart::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            SegmentPurchaseBehaviorChart::class,
            AgeGroupPurchasePatternChart::class,
            SegmentRecommendationsTable::class,
        ];
    }
}
