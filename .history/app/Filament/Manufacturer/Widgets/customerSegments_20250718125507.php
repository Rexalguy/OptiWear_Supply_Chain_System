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
            ->select('segment_label', DB::raw('COUNT(*) as customer_count'))
            ->groupBy('segment_label')
            ->orderBy('customer_count', 'desc')
            ->get();
        
        return [
            'datasets' => [
                [
                    'label' => 'Customer Segments',
                    'data' => $segments->pluck('customer_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $segments->pluck('segment_label')->toArray(),
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
                ],
                'tooltip' => [
                    'enabled' => true,
                    'backgroundColor' => 'rgba(17, 24, 39, 0.95)',
                    'titleColor' => '#F9FAFB',
                    'bodyColor' => '#F9FAFB',
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return context.label + ": " + context.raw + " customers (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
        ];
    }
}
    }
}
