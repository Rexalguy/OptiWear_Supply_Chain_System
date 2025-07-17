<?php

namespace App\Filament\Admin\Widgets;

use App\Models\OrderItem;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AdminSalesPieChart extends ChartWidget
{
    protected static ?string $heading = 'Orders by Shirt Category';
    protected static ?string $maxHeight = '225px';

    protected static ?int $sort = 2;



      protected function getData(): array
    {
        $data = DB::table('order_items as oi')
            ->join('products as p', 'oi.product_id', '=', 'p.id')
            ->join('shirt_categories as sc', 'p.shirt_category_id', '=', 'sc.id')
            ->select('sc.category', DB::raw('COUNT(oi.id) as order_count'))
            ->groupBy('sc.category')
            ->get();

        return [
            'labels' => $data->pluck('category')->toArray(),
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data->pluck('order_count')->toArray(),
                    'backgroundColor' => [
                        '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899',
                    ],
                    'borderWidth' => 1,
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => [
                            'boxWidth' => 20,
                            'padding' => 15,
                            'font' => ['size' => 14],
                        ],
                    ],
                ],
                'layout' => [
                    'padding' => ['top' => 10, 'bottom' => 10],
                ],
            ],
        ];
    }
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
