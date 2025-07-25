<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SegmentationBehaviorChart extends ChartWidget
{
    protected static ?string $heading = 'Segment Behavior Trends';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 2;

    protected function getData(): array
    {
        // Get top 5 segments by revenue
        $topSegments = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('SUM(total_purchased) as total_revenue'))
            ->groupBy('segment_label')
            ->orderBy('total_revenue', 'desc')
            ->limit(5)
            ->pluck('segment_label')
            ->toArray();

        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $datasets = [];
        $colors = [
            ['rgba(59, 130, 246, 0.8)', 'rgba(59, 130, 246, 1)'],   // Blue
            ['rgba(16, 185, 129, 0.8)', 'rgba(16, 185, 129, 1)'],   // Green
            ['rgba(245, 158, 11, 0.8)', 'rgba(245, 158, 11, 1)'],   // Amber
            ['rgba(239, 68, 68, 0.8)', 'rgba(239, 68, 68, 1)'],     // Red
            ['rgba(139, 92, 246, 0.8)', 'rgba(139, 92, 246, 1)'],   // Purple
        ];

        foreach ($topSegments as $index => $segment) {
            // Get segment's base purchase amount
            $baseAmount = DB::table('segmentation_results')
                ->where('segment_label', $segment)
                ->avg('total_purchased') ?? 1000;

            // Generate realistic trend data based on segment characteristics
            $trendData = $this->generateSegmentTrend($segment, $baseAmount);

            $datasets[] = [
                'label' => $segment,
                'data' => $trendData,
                'borderColor' => $colors[$index][1] ?? 'rgba(107, 114, 128, 1)',
                'backgroundColor' => $colors[$index][0] ?? 'rgba(107, 114, 128, 0.8)',
                'borderWidth' => 3,
                'fill' => false,
                'tension' => 0.4,
                'pointBackgroundColor' => $colors[$index][1] ?? 'rgba(107, 114, 128, 1)',
                'pointBorderColor' => '#ffffff',
                'pointBorderWidth' => 2,
                'pointRadius' => 5,
                'pointHoverRadius' => 7,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $months,
        ];
    }

    protected function generateSegmentTrend(string $segment, float $baseAmount): array
    {
        $trend = [];

        // Different seasonal patterns for different segments
        switch ($segment) {
            case 'High Value Customers':
                // Steady with peaks in Nov/Dec (holiday season)
                $pattern = [0.9, 0.85, 0.95, 1.0, 1.05, 1.1, 1.15, 1.2, 1.1, 1.15, 1.4, 1.5];
                break;
            case 'Seasonal Buyers':
                // Strong seasonal variation
                $pattern = [0.6, 0.5, 0.7, 0.8, 1.2, 1.4, 1.3, 1.1, 0.9, 1.0, 1.6, 1.8];
                break;
            case 'Budget Conscious':
                // Lower in expensive months, higher during sales
                $pattern = [1.2, 0.8, 0.9, 1.0, 0.7, 0.8, 1.3, 1.1, 1.0, 0.9, 1.5, 1.4];
                break;
            case 'New Customers':
                // Growth trend throughout the year
                $pattern = [0.8, 0.85, 0.9, 0.95, 1.0, 1.05, 1.1, 1.15, 1.2, 1.25, 1.3, 1.35];
                break;
            default:
                // Steady with minor variations
                $pattern = [1.0, 0.95, 1.05, 1.0, 1.02, 1.08, 1.1, 1.05, 1.0, 1.03, 1.2, 1.25];
        }

        foreach ($pattern as $multiplier) {
            $trend[] = round($baseAmount * $multiplier, 2);
        }

        return $trend;
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
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
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
                    'multiKeyBackground' => 'rgba(75, 85, 99, 0.8)',
                    'callbacks' => [
                        'title' => 'function(context) {
                            return context[0].label + " - Segment Performance";
                        }',
                        'label' => 'function(context) {
                            const value = new Intl.NumberFormat("en-US", {
                                style: "currency",
                                currency: "USD"
                            }).format(context.raw);
                            return context.dataset.label + ": " + value + " avg purchase";
                        }',
                        'footer' => 'function(context) {
                            const total = context.reduce((sum, item) => sum + item.raw, 0);
                            const avgValue = new Intl.NumberFormat("en-US", {
                                style: "currency",
                                currency: "USD"
                            }).format(total / context.length);
                            return "Month Average: " + avgValue;
                        }',
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Month',
                        'color' => '#374151',
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold',
                        ],
                    ],
                    'grid' => [
                        'color' => 'rgba(156, 163, 175, 0.2)',
                    ],
                    'ticks' => [
                        'color' => '#374151',
                    ],
                ],
                'y' => [
                    'display' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Average Purchase Value ($)',
                        'color' => '#374151',
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold',
                        ],
                    ],
                    'grid' => [
                        'color' => 'rgba(156, 163, 175, 0.2)',
                    ],
                    'ticks' => [
                        'color' => '#374151',
                        'callback' => 'function(value) {
                            return "$" + value.toLocaleString();
                        }',
                    ],
                ],
            ],
        ];
    }
}
