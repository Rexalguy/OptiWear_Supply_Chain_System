<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SegmentStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Get segment statistics
        $segmentData = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('SUM(total_purchased) as total_purchases'))
            ->groupBy('segment_label')
            ->get();

        $totalSegments = $segmentData->count();
        $totalPurchases = $segmentData->sum('total_purchases');
        
        $topSegment = $segmentData->sortByDesc('total_purchases')->first();
        $averagePurchases = $totalSegments > 0 ? round($totalPurchases / $totalSegments, 1) : 0;

        return [
            Stat::make('Total Segments', $totalSegments)
                ->description('Customer segments identified')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Most Active Segment', $topSegment->segment_label ?? 'N/A')
                ->description($topSegment ? number_format($topSegment->total_purchases) . ' purchases' : 'No data')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),

            Stat::make('Total Purchases', number_format($totalPurchases))
                ->description('Across all segments')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('warning'),

            Stat::make('Average per Segment', number_format($averagePurchases))
                ->description('Average purchases per segment')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
        ];
    }
}
