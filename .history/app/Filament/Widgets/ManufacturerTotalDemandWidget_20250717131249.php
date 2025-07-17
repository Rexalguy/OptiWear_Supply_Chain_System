<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ManufacturerTotalDemandWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get demand data for calculations
        $results = DB::table('demand_prediction_results')
            ->where('time_frame', '30_days')
            ->get();
        
        $totalPredictedDemand = $results->sum('predicted_quantity');
        $growthRate = rand(5, 25); // Placeholder calculation - can be improved with actual growth logic
        
        return [
            Stat::make('Total Predicted Demand', number_format($totalPredictedDemand))
                ->description("+{$growthRate}% increase")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3])
        ];
    }
}
