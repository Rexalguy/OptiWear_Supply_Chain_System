<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AdminTopSalesBar extends ChartWidget
{
    protected static ?string $heading = 'Top Selling Products';
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static string $chartType = 'bar';

    protected static ?string $height = '700px';


    protected function getData(): array
    {
        $data = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return [
            'labels' => $data->pluck('name'),
            'datasets' => [
                [
                    'label' => 'Units Sold',
                    'data' => $data->pluck('total_sold'),
                    'backgroundColor' => [
                        '#3B82F6',
                        '#10B981',
                        '#F59E0B',
                        '#EF4444',
                        '#8B5CF6',
                    ],
                    'borderRadius' => 1,
                    'borderColor' => 'transparent',
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            //
        ];
    }



    protected function getType(): string
    {
        return 'bar';
    }
}
