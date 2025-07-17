<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SegmentDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Customer Segment Distribution';
    
    protected function getData(): array
    {
        // Get total customers per segment
        $segmentData = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('SUM(total_purchased) as total_purchases'))
            ->groupBy('segment_label')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColor = [
            'rgba(255, 99, 132, 0.8)',   // Red
            'rgba(54, 162, 235, 0.8)',   // Blue  
            'rgba(255, 205, 86, 0.8)',   // Yellow
            'rgba(75, 192, 192, 0.8)',   // Green
            'rgba(153, 102, 255, 0.8)',  // Purple
            'rgba(255, 159, 64, 0.8)',   // Orange
        ];

        // Calculate total first
        $totalPurchases = $segmentData->sum('total_purchases');

        foreach ($segmentData as $index => $segment) {
            $percentage = $totalPurchases > 0 ? round(($segment->total_purchases / $totalPurchases) * 100, 1) : 0;
            $labels[] = $segment->segment_label . ' (' . $percentage . '%)';
            $data[] = $percentage;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Segment Distribution',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColor, 0, count($data)),
                    'borderWidth' => 2,
                    'borderColor' => 'rgba(255, 255, 255, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
            ],
        ];
    }
}
