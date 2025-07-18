<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class AdminSalesChart extends ChartWidget
{
     protected static ?string $heading = '🗓️ Mothly Sales';

     protected static ?int $sort = 3;


    protected function getData(): array
    {
        $monthlySales = collect();
        $now = now();

        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i)->startOfMonth();
            $label = $month->format('M');

            $monthlyTotal = Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total');

            $monthlySales->push([
                'label' => $label,
                'total' => round($monthlyTotal),
            ]);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $monthlySales->pluck('total'),
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.3)',
                ],
            ],
            'labels' => $monthlySales->pluck('label'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
