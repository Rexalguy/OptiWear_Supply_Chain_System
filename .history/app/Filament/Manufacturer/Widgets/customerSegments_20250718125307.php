<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class customerSegments extends ChartWidget
{
    protected static ?string $heading = 'Customer Segment Distribution';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        $segments = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('COUNT(*) as customer_count'), DB::raw('SUM(total_purchased) as total_revenue'))
            ->groupBy('segment_label')
            ->orderBy('customer_count', 'desc')
            ->get();

        $totalCustomers = $segments->sum('customer_count');
        
        return [
            'datasets' => [
                [
                    'label' => 'Customer Segments',
                    'data' => $segments->pluck('customer_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',   // Blue
                        'rgba(16, 185, 129, 0.8)',   // Green
            'rgba(245, 158, 11, 0.8)',   // Amber
            'rgba(139, 92, 246, 0.8)',   // Purple
            'rgba(239, 68, 68, 0.8)',    // Red
            'rgba(6, 182, 212, 0.8)',    // Cyan
            'rgba(236, 72, 153, 0.8)',   // Pink
            'rgba(34, 197, 94, 0.8)',    // Emerald
        ];
        
        $borderColors = [
            'rgba(59, 130, 246, 1)',
            'rgba(16, 185, 129, 1)',
            'rgba(245, 158, 11, 1)',
            'rgba(139, 92, 246, 1)',
            'rgba(239, 68, 68, 1)',
            'rgba(6, 182, 212, 1)',
            'rgba(236, 72, 153, 1)',
            'rgba(34, 197, 94, 1)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Customer Segments',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_slice($borderColors, 0, count($data)),
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
                    'position' => 'right',
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
                        'title' => 'function(context) { return context[0].label; }',
                        'label' => 'function(context) { 
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ": " + context.parsed + " customers (" + percentage + "%)"; 
                        }'
                    ]
                ]
            ],
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(255, 255, 255, 0.1)'
                    ],
                    'pointLabels' => [
                        'color' => '#fff'
                    ],
                    'ticks' => [
                        'color' => '#fff',
                        'backdropColor' => 'transparent'
                    ]
                ],
            ],
        ];
    }
}
