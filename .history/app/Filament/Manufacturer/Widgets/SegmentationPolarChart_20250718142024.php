<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;

class SegmentationPolarChart extends ChartWidget
{
    protected static ?string $heading = 'Customer Segmentation Overview';
    
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Segments',
                    'data' => [],
                    'backgroundColor' => [
                        '#FF6384', // Pink/Red
                        '#36A2EB', // Blue
                        '#FFCE56', // Yellow
                        '#4BC0C0', // Teal
                        '#9966FF', // Purple
                        '#FF9F40', // Orange
                        '#FF6384', // Pink (repeat if more segments)
                        '#C9CBCF', // Grey
                    ],
                ],
            ],
            'labels' => [],
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
}
