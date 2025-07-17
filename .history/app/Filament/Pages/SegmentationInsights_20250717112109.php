<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SegmentationInsights extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Segmentation Insights';
    protected static ?string $navigationGroup = 'Analytics';
    protected static string $view = 'filament.pages.segmentation-insights';
}
