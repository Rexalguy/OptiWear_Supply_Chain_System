<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\DemandPredictionChart;

class Analytics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.analytics';

    protected function getHeaderWidgets(): array
    {
        return [
            DemandPredictionChart::class,
        ];
    }
}
