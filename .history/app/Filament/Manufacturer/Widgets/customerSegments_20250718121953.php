<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class customerSegments extends ChartWidget
{
    protected static ?string $heading = 'Customer Segments Distribution';
    protected int | string | array $columnSpan = 'full';
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        // Get segment distribution from segmentation_results table
        $segmentData = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('COUNT(*) as count'))
            ->groupBy('segment_label')
            ->orderBy('count', 'desc')
            ->get();

        if ($segmentData->isEmpty()) {
            // No data available
            return [
                'datasets' => [
                    [
                        'label' => 'Customer Segments',
                        'data' => [],
                        'backgroundColor' => [],
                        'borderColor' => [],
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => [],
            ];
        }

        // Extract labels and data from database results
        $labels = $segmentData->pluck('segment_label')->toArray();
        $data = $segmentData->pluck('count')->toArray();

        // Generate colors for segments
        $colors = [
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
                        'High-Value Customers',
                        'Frequent Buyers',
                        'Price-Sensitive',
                        'Casual Shoppers',
                        'Premium Seekers',
                        'Seasonal Buyers'
                    ],
                ];
            }

            $labels = [];
            $data = [];
            $colors = [
                'rgba(59, 130, 246, 0.8)',   // Blue
                'rgba(16, 185, 129, 0.8)',   // Green
                'rgba(245, 158, 11, 0.8)',   // Amber
                'rgba(139, 92, 246, 0.8)',   // Purple
                'rgba(239, 68, 68, 0.8)',    // Red
                'rgba(6, 182, 212, 0.8)',    // Cyan
                'rgba(168, 85, 247, 0.8)',   // Violet
                'rgba(34, 197, 94, 0.8)',    // Emerald
            ];

            $borderColors = [
                'rgba(59, 130, 246, 1)',
                'rgba(16, 185, 129, 1)',
                'rgba(245, 158, 11, 1)',
                'rgba(139, 92, 246, 1)',
                'rgba(239, 68, 68, 1)',
                'rgba(6, 182, 212, 1)',
                'rgba(168, 85, 247, 1)',
                'rgba(34, 197, 94, 1)',
            ];

            foreach ($segmentData as $index => $segment) {
                $labels[] = $segment->segment_label;
                $data[] = $segment->count;
            }

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
        } catch (\Exception $e) {
            // Fallback data in case of error
            return [
                'datasets' => [
                    [
                        'label' => 'Error Loading Data',
                        'data' => [1],
                        'backgroundColor' => ['rgba(239, 68, 68, 0.8)'],
                        'borderColor' => ['rgba(239, 68, 68, 1)'],
                        'borderWidth' => 2,
                    ],
                ],
                'labels' => ['Data Unavailable'],
            ];
        }
    }

    protected function getType(): string
    {
        return 'polarArea';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 0, 0, 0.1)',
                    ],
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
