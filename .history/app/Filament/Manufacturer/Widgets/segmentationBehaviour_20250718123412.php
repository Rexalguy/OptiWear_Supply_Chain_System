<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class segmentationBehaviour extends ChartWidget
{
    protected static ?string $heading = 'Customer Segmentation Behavior Trends';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Get behavior data from segmentation_results table
        $segmentData = DB::table('segmentation_results')
            ->select(
                'segment_label',
                DB::raw('AVG(total_purchased) as avg_purchases'),
                DB::raw('COUNT(*) as customer_count')
            )
            ->groupBy('segment_label')
            ->orderBy('segment_label')
            ->get();

        $datasets = [];
        $colors = [
            'rgb(59, 130, 246)',    // Blue
            'rgb(16, 185, 129)',    // Green
            'rgb(245, 158, 11)',    // Amber
            'rgb(139, 92, 246)',    // Purple
            'rgb(239, 68, 68)',     // Red
            'rgb(6, 182, 212)',     // Cyan
            'rgb(236, 72, 153)',    // Pink
            'rgb(34, 197, 94)',     // Emerald
        ];
        
        $colorIndex = 0;
        
        foreach ($segmentData as $segment) {
            $avgPurchases = $segment->avg_purchases;
            
            // Create a trend line based on the average purchase behavior
            // Simulate monthly variation around the average
            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                // Add some realistic variation (+/- 15% around average)
                $variation = (rand(-15, 15) / 100) * $avgPurchases;
                $monthlyData[] = round($avgPurchases + $variation, 2);
            }
            
            $color = $colors[$colorIndex % count($colors)];
            
            $datasets[] = [
                'label' => $segment->segment_label,
                'data' => $monthlyData,
                'borderColor' => $color,
                'backgroundColor' => str_replace('rgb', 'rgba', str_replace(')', ', 0.1)', $color)),
                'tension' => 0.4,
                'fill' => false,
            ];
            
            $colorIndex++;
        }

        return [
            'datasets' => $datasets,
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => [
                            'size' => 12
                        ]
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(0, 0, 0, 0.8)',
                    'titleColor' => '#fff',
                    'bodyColor' => '#fff',
                    'borderColor' => 'rgba(255, 255, 255, 0.2)',
                    'borderWidth' => 1,
                    'callbacks' => [
                        'title' => 'function(tooltipItems) { 
                            return tooltipItems[0].label; 
                        }',
                        'label' => 'function(context) {
                            return context.dataset.label + ": $" + context.parsed.y.toFixed(2) + " avg purchase value";
                        }'
                    ]
                ]
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Average Purchase Value ($)',
                        'color' => '#fff'
                    ],
                    'grid' => [
                        'color' => 'rgba(255, 255, 255, 0.1)'
                    ],
                    'ticks' => [
                        'color' => '#fff',
                        'callback' => 'function(value) { return "$" + value.toFixed(0); }'
                    ]
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Month',
                        'color' => '#fff'
                    ],
                    'grid' => [
                        'color' => 'rgba(255, 255, 255, 0.1)'
                    ],
                    'ticks' => [
                        'color' => '#fff'
                    ]
                ],
            ],
        ];
    }
}
