<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SegmentPurchaseBehaviorChart extends ChartWidget
{
    protected static ?string $heading = 'Purchase Behavior by Customer Segment';
    
    protected function getData(): array
    {
        // Get total purchases per segment
        $segmentData = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('SUM(total_purchased) as total_purchases'))
            ->groupBy('segment_label')
            ->orderBy('total_purchases', 'desc')
            ->get();

        $labels = [];
        $data = [];

        foreach ($segmentData as $segment) {
            $labels[] = $segment->segment_label;
            $data[] = (float) $segment->total_purchases;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Purchases',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                    'borderColor' => '#36A2EB',
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

    protected function getOptions(): array
    {
        return [
            'responsive' => false,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Total Purchases'
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Customer Segments'
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
