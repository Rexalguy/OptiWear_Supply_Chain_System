<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;

class SegmentationBarChart extends ChartWidget
{
    protected static ?string $heading = 'Segment Analysis';
    
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Data',
                    'data' => [],
                    'backgroundColor' => '#87CEEB', // Light blue
                    'borderColor' => '#4682B4', // Steel blue border
                    'borderWidth' => 1,
                ],
            ],
            'labels' => [],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
