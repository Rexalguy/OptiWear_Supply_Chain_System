<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class SegmentationTable extends Widget
{
    protected static string $view = 'filament.widgets.segmentation-table';
    
    protected int | string | array $columnSpan = 1;

    protected function getViewData(): array
    {
        // Get the segment data with percentage contribution
        $segmentData = DB::table('segmentation_results')
            ->select([
                'segment_label as segment',
                DB::raw('ROUND((SUM(total_purchased) * 100.0 / (SELECT SUM(total_purchased) FROM segmentation_results)), 1) as percentage_contribution')
            ])
            ->groupBy('segment_label')
            ->orderBy(DB::raw('ROUND((SUM(total_purchased) * 100.0 / (SELECT SUM(total_purchased) FROM segmentation_results)), 1)'), 'desc')
            ->get();

        return [
            'segments' => $segmentData,
            'heading' => 'Segment Percentage Contribution',
        ];
    }
}
