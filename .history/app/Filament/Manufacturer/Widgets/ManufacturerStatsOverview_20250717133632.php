<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ManufacturerStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
$next30 = Carbon::today()->addDays(30);
$prev30 = Carbon::today()->subDays(30);

// 1. Total predicted
$totalQuantity = ShirtCategory::whereBetween('prediction_date', [$today, $next30])->sum('predicted_quantity');

// 2. Highest category
$top = ShirtCategory::whereBetween('prediction_date', [$today, $next30])
    ->select('category', DB::raw('SUM(predicted_quantity) as total'))
    ->groupBy('category')
    ->orderByDesc('total')
    ->first();

$topCategoryName = $top->category ?? 'N/A';
$topCategoryQuantity = $top->total ?? 0;

// 3. Lowest category
$lowest = ShirtCategory::whereBetween('prediction_date', [$today, $next30])
    ->select('category', DB::raw('SUM(predicted_quantity) as total'))
    ->groupBy('category')
    ->orderBy('total')
    ->first();

$lowestCategoryName = $lowest->category ?? 'N/A';
$lowestCategoryQuantity = $lowest->total ?? 0;

// 4. Trend
$current = $totalQuantity;
$previous = ShirtCategory::whereBetween('prediction_date', [$prev30, $today])->sum('predicted_quantity');
$trendDirection = $current > $previous ? 'up' : 'down';
$trendDifference = $previous > 0 ? round((($current - $previous) / $previous) * 100, 2) : 0;
        return [
            //
        ];
    }
}
