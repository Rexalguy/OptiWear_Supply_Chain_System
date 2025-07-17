<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ManufacturerStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get demand data for next 30 days
        $startDate = Carbon::today()->addDay();
        $endDate = Carbon::today()->addDays(30);
        
        $results = DB::table('demand_prediction_results')
            ->where('time_frame', '30_days')
            ->whereBetween('prediction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();
        
        // 1. Total predicted quantity
        $totalPredictedQuantity = $results->sum('predicted_quantity');
        
        // 2. Category with highest sum
        $categoryTotals = $results->groupBy('shirt_category')
            ->map(fn($group) => $group->sum('predicted_quantity'))
            ->sortDesc();
        
        $highestCategory = $categoryTotals->keys()->first() ?? 'N/A';
        $highestCategoryTotal = $categoryTotals->first() ?? 0;
        
        // 3. Category with lowest sum
        $lowestCategory = $categoryTotals->keys()->last() ?? 'N/A';
        $lowestCategoryTotal = $categoryTotals->last() ?? 0;
        
        // 4. Trend logic: Compare this week vs next week's predictions
        $thisWeekEnd = Carbon::today()->addDays(7);
        $nextWeekEnd = Carbon::today()->addDays(14);
        
        $thisWeekTotal = $results->where('prediction_date', '<=', $thisWeekEnd->toDateString())->sum('predicted_quantity');
        $nextWeekTotal = $results->where('prediction_date', '>', $thisWeekEnd->toDateString())
                                ->where('prediction_date', '<=', $nextWeekEnd->toDateString())
                                ->sum('predicted_quantity');
        
        $trendPercentage = 0;
        $trendDirection = 'stable';
        $trendColor = 'warning';
        $trendIcon = 'heroicon-m-minus';
        
        if ($thisWeekTotal > 0) {
            $trendPercentage = round((($nextWeekTotal - $thisWeekTotal) / $thisWeekTotal) * 100, 1);
            
            if ($trendPercentage > 0) {
                $trendDirection = 'increasing';
                $trendColor = 'success';
                $trendIcon = 'heroicon-m-arrow-trending-up';
            } elseif ($trendPercentage < 0) {
                $trendDirection = 'decreasing';
                $trendColor = 'danger';
                $trendIcon = 'heroicon-m-arrow-trending-down';
                $trendPercentage = abs($trendPercentage); // Show positive number
            }
        }
        
        return [
            Stat::make('Total Predicted Demand', number_format($totalPredictedQuantity))
                ->description('Next 30 days forecast')
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color('primary'),
                
            Stat::make('Top Performing Category', $highestCategory)
                ->description(number_format($highestCategoryTotal) . ' units predicted')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
                
            Stat::make('Lowest Demand Category', $lowestCategory)
                ->description(number_format($lowestCategoryTotal) . ' units predicted')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger'),
                
            Stat::make('Weekly Trend', ucfirst($trendDirection))
                ->description($trendPercentage . '% week-over-week change')
                ->descriptionIcon($trendIcon)
                ->color($trendColor),
        ];
    }
}
