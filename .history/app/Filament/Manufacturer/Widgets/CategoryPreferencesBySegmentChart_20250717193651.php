<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class CategoryPreferencesBySegmentChart extends ChartWidget
{
    protected static ?string $heading = 'Category Preferences by Customer Segment';
    
    protected function getData(): array
    {
        // Get all segments and categories
        $segmentData = DB::table('segmentation_results')
            ->select('segment_label', 'shirt_category', 'total_purchased')
            ->get();

        $segments = $segmentData->pluck('segment_label')->unique()->values();
        $categories = $segmentData->pluck('shirt_category')->unique()->values();

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
