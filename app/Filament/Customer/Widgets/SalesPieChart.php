<?php

namespace App\Filament\Customer\Widgets;

use Filament\Widgets\ChartWidget;

class SalesPieChart extends ChartWidget
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
        return 'pie';
    }
}
