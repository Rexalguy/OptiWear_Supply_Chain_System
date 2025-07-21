<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use App\Models\SegmentationResult;

class SegmentationPolarChart extends ChartWidget
{
    protected static ?string $heading = 'Customer ';

    protected static ?string $maxHeight = '400px';

    protected static ?string $description = 'Number of customers in each segment for their most popular product category';
    
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // Get the same data as the table - top product for each segment
        $segmentData = SegmentationResult::select([
            'segment_label',
            'shirt_category',
            DB::raw('SUM(total_purchased) as total_purchased'),
            DB::raw('SUM(customer_count) as customer_count')
        ])
        ->groupBy('segment_label', 'shirt_category')
        ->havingRaw('SUM(total_purchased) > 0')
        ->havingRaw('SUM(customer_count) > 0') // Only segments with actual customers
        ->orderBy('segment_label')
        ->orderByDesc('total_purchased')
        ->get();

        // Get the top product for each segment (same logic as table)
        $topProducts = $segmentData->groupBy('segment_label')->map(function ($products) {
            return $products->first(); // Gets the highest total_purchased for each segment
        })->take(8)->values(); // Limit to first 8 segments to match table

        $labels = $topProducts->pluck('segment_label')->toArray();
        $data = $topProducts->pluck('customer_count')->toArray();

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
                    'backgroundColor' => '#333',
                    'borderWidth' => 2,
                    'cornerRadius' => 8,
                    
                    
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
                    'ticks' =>[
                        'display' => false,
                    ],
                    
                ],

                'x'=>[
                    'display' =>false,
                ],

                'y'=>[
                    'display' =>false,
                ]
            ],
            'animation' =>[
                'duration' => 1500,
                'easing' => 'easeInOutQuart',
            ]
        ];
    }
}
