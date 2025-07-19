<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SegmentationPolarChart extends ChartWidget
{
    protected static ?string $heading = 'Customer Segmentation Overview';

    protected static ?string $maxHeight = '400px';
    
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // Query to get customer count by segment_label
        $segmentData = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('SUM(customer_count) as total_customers'))
            ->groupBy('segment_label')
            ->orderBy('total_customers', 'desc')
            ->get();

        $labels = $segmentData->pluck('segment_label')->toArray();
        $data = $segmentData->pluck('total_customers')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Customers',
                    'data' => $data,
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.8)',   // Pink/Red
                        'rgba(54, 162, 235, 0.8)',   // Blue
                        'rgba(255, 206, 86, 0.8)',   // Yellow
                        'rgba(75, 192, 192, 0.8)',   // Teal
                        'rgba(153, 102, 255, 0.8)',  // Purple
                        'rgba(255, 159, 64, 0.8)',   // Orange
                        'rgba(199, 199, 199, 0.8)',  // Grey
                        'rgba(83, 102, 255, 0.8)',   // Blue variant
                    ],
                    'borderColor' => [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(199, 199, 199, 1)',
                        'rgba(83, 102, 255, 1)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
                        'font' => [
                            'size' => 11,
                        ],
                        'color' => '#374151',
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.95)',
                    'titleColor' => '#F9FAFB',
                    'bodyColor' => '#F9FAFB',
                    'borderColor' => '#6B7280',
                    'borderWidth' => 1,
                    'cornerRadius' => 8,
                    'displayColors' => true,
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label + ": " + context.raw + " customers (" + percentage + "%)";
                        }',
                        'title' => 'function(context) {
                            return "Customer Segment";
                        }',
                    ],
                ],
            ],
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' =>false,
                    ],
                    'angleLines' => [
                        'display' =>false,
                    ],
                    
                ],

                'x'=>[
                    'display' =>false,
                ],

                'y'=>[
                    'display' =>false,
                ]
            ],
            'animation' =>
        ];
    }
}
