<?php

namespace App\Filament\Customer\Widgets;

use Filament\Widgets\ChartWidget;

class PurchaseChart extends ChartWidget
{
    protected static ?string $heading = 'Purchase Orders chart';

    protected static ?int $sort = 2;
    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Purchase orders',
                    'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
                ],
            ],
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
