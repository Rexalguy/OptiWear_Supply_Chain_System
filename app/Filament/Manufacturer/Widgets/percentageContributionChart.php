<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;

class percentageContributionChart extends ChartWidget
{
    protected static ?string $heading = 'Chart';

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
