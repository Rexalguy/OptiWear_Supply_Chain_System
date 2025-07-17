<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SegmentPurchaseBehaviorChart extends ChartWidget
{
    protected static ?string $heading = 'Purchase Behavior by Customer Segment';

    protected static ?string $Height = ;
    
    protected function getData(): array
    {
        try {
            // Get total purchases per segment with performance limit
            $segmentData = DB::table('segmentation_results')
                ->select('segment_label', DB::raw('SUM(total_purchased) as total_purchases'))
                ->groupBy('segment_label')
                ->orderBy('total_purchases', 'desc')
                ->limit(10)
                ->get();

            if ($segmentData->isEmpty()) {
                return [
                    'datasets' => [
                        [
                            'label' => 'No Data Available',
                            'data' => [0],
                            'backgroundColor' => 'rgba(156, 163, 175, 0.7)',
                        ],
                    ],
                    'labels' => ['No segmentation data'],
                ];
            }

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
        } catch (\Exception $e) {
            return [
                'datasets' => [
                    [
                        'label' => 'Database Error',
                        'data' => [0],
                        'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    ],
                ],
                'labels' => ['Error loading data'],
            ];
        }
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
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
