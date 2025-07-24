<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class BubbleSalesChart extends ChartWidget
{


    protected static ?string $heading = 'Customer Purchase Behavior';

    protected static ?string $description = 'A bubble chart visualizing customer purchasing patterns—total orders vs. total revenue, with bubble size representing average order value';
    protected static ?string $maxHeight = '400px';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = DB::table('products')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->select(
                'products.name',
                DB::raw('COUNT(order_items.id) as total_orders'),
                DB::raw('SUM(order_items.quantity * order_items.unit_price) as total_revenue'),
                DB::raw('AVG(order_items.quantity * order_items.unit_price) as avg_order_value')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $bubbles = $data->map(function ($item) {
            return [
                'x' => (int) $item->total_orders,
                'y' => (float) $item->total_revenue,
                'r' => max(2, min(8, $item->avg_order_value / 500)), // scaled radius
                'label' => $item->name,
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Top Products',
                    'data' => $bubbles,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 0,
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Total Orders',
                    ],
                    'beginAtZero' => true,
                ],
                'y' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Total Revenue (UGX)',
                    ],
                    'beginAtZero' => true,
                ],


            ],

            'animation' => [
                'duration' => 2000,
                'easing' => 'easeInOutCubic',
            ],
            'plugins' => [],
        ];
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}
