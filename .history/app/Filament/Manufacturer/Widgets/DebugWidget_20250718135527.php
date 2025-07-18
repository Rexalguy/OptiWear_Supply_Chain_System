<?php

namespace App\Filament\Manufacturer\Widgets;

use App\Models\SegmentationResult;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class DebugWidget extends Widget
{
    protected static string $view = 'filament.widgets.debug-widget';

    public function getViewData(): array
    {
        // Test the actual queries step by step
        $totalRecords = SegmentationResult::count();
        
        $segmentCounts = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('COUNT(*) as count'))
            ->groupBy('segment_label')
            ->get();
            
        $segmentCountsWithPercentage = DB::table('segmentation_results')
            ->select([
                'segment_label',
                DB::raw('COUNT(*) as count'),
                DB::raw('ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM segmentation_results)), 1) as percentage')
            ])
            ->groupBy('segment_label')
            ->get();

        return [
            'totalRecords' => $totalRecords,
            'segmentCounts' => $segmentCounts,
            'segmentCountsWithPercentage' => $segmentCountsWithPercentage,
        ];
    }
}
