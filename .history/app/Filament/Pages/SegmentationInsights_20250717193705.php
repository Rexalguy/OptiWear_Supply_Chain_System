<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Manufacturer\Widgets\SegmentDistributionChart;
use App\Filament\Manufacturer\Widgets\SegmentPurchaseBehaviorChart;
use App\Filament\Manufacturer\Widgets\CategoryPreferencesBySegmentChart;

class SegmentationInsights extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Segmentation Insights';
    protected static ?string $navigationGroup = 'Analytics';
    protected static string $view = 'filament.pages.segmentation-insights';

    protected function getHeaderWidgets(): array
    {
        return [
            SegmentDistributionChart::class,
            SegmentPurchaseBehaviorChart::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            CategoryPreferencesBySegmentChart::class,
        ];
    }
}
