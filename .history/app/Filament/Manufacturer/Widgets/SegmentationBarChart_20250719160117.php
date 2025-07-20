<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SegmentationBarChart extends ChartWidget
{
    protected static ?string $heading = 'Total Products Purchased Per Segment';
 
    protected static ?string $description = 'C';
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // Query to get total quantity purchased by each segment
        $segmentData = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('SUM(total_purchased) as total_quantity'))
            ->groupBy('segment_label')
            ->orderBy('total_quantity', 'desc')
            ->get();

        $labels = $segmentData->pluck('segment_label')->toArray();
        $data = $segmentData->pluck('total_quantity')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Products Purchased',
                    'data' => $data,
                    'backgroundColor' => '#87CEEB', // Light blue
                    'borderColor' => '#4682B4', // Steel blue border
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
