<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;

class segmentationBehaviour extends ChartWidget
{
    protected static ?string $heading = 'Segmentation Behaviou';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
