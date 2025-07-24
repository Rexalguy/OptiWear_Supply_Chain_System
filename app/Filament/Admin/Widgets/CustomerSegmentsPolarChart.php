<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CustomerSegmentsPolarChart extends ChartWidget
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
                        'rgba(239, 68, 68, 0.8)',    // Red
                        'rgba(139, 92, 246, 0.8)',   // Purple
                        'rgba(236, 72, 153, 0.8)',   // Pink
                        'rgba(34, 197, 94, 0.8)',    // Emerald
                        'rgba(249, 115, 22, 0.8)',   // Orange
                    ],
                    'borderColor' => [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(249, 115, 22, 1)',
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
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
                        'font' => [
                            'size' => 12,
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
                        'color' => 'rgba(156, 163, 175, 0.3)',
                    ],
                    'angleLines' => [
                        'color' => 'rgba(156, 163, 175, 0.3)',
                    ],
                    'pointLabels' => [
                        'color' => '#374151',
                        'font' => [
                            'size' => 11,
                        ],
                    ],
                ],
            ],
        ];
    }
}
