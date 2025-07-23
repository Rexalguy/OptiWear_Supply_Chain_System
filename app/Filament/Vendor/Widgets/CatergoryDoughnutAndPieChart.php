<?php

namespace App\Filament\Vendor\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\VendorOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CatergoryDoughnutAndPieChart extends ChartWidget
{
    protected static ?string $heading = 'Orders by Category';
    protected static ?string $description = 'Distribution of orders across different product categories';
    protected int $refreshInterval = 300; // Refresh every 5 minutes
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '250px';

    protected function getData(): array
    {
        // Get order items grouped by shirt category with total quantities
        $categoryData = VendorOrderItem::join('products', 'vendor_order_items.product_id', '=', 'products.id')
            ->join('shirt_categories', 'products.shirt_category_id', '=', 'shirt_categories.id')
            ->select('shirt_categories.category as category_name', DB::raw('SUM(vendor_order_items.quantity) as total_quantity'))
            ->groupBy('shirt_categories.id', 'shirt_categories.category')
            ->get();

        // Prepare data for the chart
        $labels = $categoryData->pluck('category_name')->toArray();
        $quantities = $categoryData->pluck('total_quantity')->toArray();

        // Define colors for different categories
        $colors = [
            '#3b82f6', // blue
            '#ef4444', // red
            '#10b981', // green
            '#f59e0b', // yellow
            '#8b5cf6', // purple
            '#ec4899', // pink
            '#6366f1', // indigo
            '#14b8a6', // teal
            '#f97316', // orange
            '#6b7280', // gray
        ];

        // Ensure we have enough colors
        while (count($colors) < count($labels)) {
            $colors = array_merge($colors, $colors);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders by Category',
                    'data' => $quantities,
                    'backgroundColor' => array_slice($colors, 0, count($labels)),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                    'hoverOffset' => 15,
                ]
            ],
            'labels' => $labels
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                    'labels' => [
                        'padding' => 20,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
            ],
            'cutout' => '60%',
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
