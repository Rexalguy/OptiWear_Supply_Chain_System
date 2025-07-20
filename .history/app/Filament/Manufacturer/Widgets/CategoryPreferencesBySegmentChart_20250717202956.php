<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CategoryPreferencesBySegmentChart extends ChartWidget
{
    protected static ?string $heading = 'Category Preferences by Customer Segment';
    protected static ?string $height = '450px';
    protected int | string | array $columnSpan = 8;
    
    public function getWidgetAttributes(): array
    {
        return [
            'data-widget' => 'category-preferences',
            'class' => 'category-preferences-widget',
        ];
    }
    
    protected function getData(): array
    {
        try {
            // Get all segments and categories with performance limits
            $segmentData = DB::table('segmentation_results')
                ->select('segment_label', 'shirt_category', 'total_purchased')
                ->limit(100) // Prevent excessive processing
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

            $segments = $segmentData->pluck('segment_label')->unique()->values()->take(8); // Limit segments
            $categories = $segmentData->pluck('shirt_category')->unique()->values()->take(5); // Limit categories

            $labels = $segments->toArray();
            $datasets = [];

            $colors = [
                'rgba(255, 99, 132, 0.7)',   // Red
                'rgba(54, 162, 235, 0.7)',   // Blue  
                'rgba(255, 205, 86, 0.7)',   // Yellow
                'rgba(75, 192, 192, 0.7)',   // Green
                'rgba(153, 102, 255, 0.7)',  // Purple
            ];

            // Create dataset for each category
            foreach ($categories as $index => $category) {
                $data = [];
                
                foreach ($segments as $segment) {
                    $purchases = $segmentData
                        ->where('segment_label', $segment)
                        ->where('shirt_category', $category)
                        ->sum('total_purchased');
                    
                    $data[] = (float) $purchases;
                }

                $datasets[] = [
                    'label' => ucfirst(str_replace('_', ' ', $category)),
                    'data' => $data,
                    'backgroundColor' => $colors[$index % count($colors)],
                    'borderColor' => str_replace('0.7', '1', $colors[$index % count($colors)]),
                    'borderWidth' => 1,
                ];
            }

            return [
                'datasets' => $datasets,
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
                'x' => [
                    'stacked' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Customer Segments'
                    ],
                ],
                'y' => [
                    'stacked' => true,
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Total Purchases'
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
        ];
    }
}
