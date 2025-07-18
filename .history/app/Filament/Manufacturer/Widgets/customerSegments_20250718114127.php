<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;

class customerSegments extends ChartWidget
{
    protected static ?string $heading = 'Customer Segments';

    protected function getData(): array
    {
        return [
            //
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
}
