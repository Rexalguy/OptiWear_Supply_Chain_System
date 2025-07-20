<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ManufacturerActivePredictionsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get count of active predictions
        $results = DB::table('demand_prediction_results')
            ->where('time_frame', '30_days')
            ->get();
        
        $activePredictions = $results->count();
        
        return [
            Stat::make('Active Predictions', $activePredictions)
                ->description('Data points')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary')
        ];
    }
}
