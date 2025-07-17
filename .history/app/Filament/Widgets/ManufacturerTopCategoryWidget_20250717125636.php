<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class ManufacturerTopCategoryWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get demand data for calculations
        $results = DB::table('demand_prediction_results')
            ->where('time_frame', '30_days')
            ->get();
        
        $highestCategory = $results->groupBy('shirt_category')
            ->map(fn($group) => $group->sum('predicted_quantity'))
            ->sortDesc()
            ->keys()
            ->first() ?? 'N/A';
        
        return [
            Stat::make('Top Category', $highestCategory)
                ->description('Highest demand')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning')
        ];
    }
}
