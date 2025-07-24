<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ProductsBarsLine extends ChartWidget
{
    protected static ?int $sort = 2;

    protected static ?string $heading = 'Top 10 Products: Stock vs Orders';
    protected static ?string $description = 'This chart compares available stock quantities with order counts for the top 10 products.';
    protected int | string | array $columnSpan = 8;

    protected static string $chartType = 'bar';

    protected static ?string $height = '450px';

    protected function getData(): array
    {
        $products = Product::withCount('orderItems')
            ->orderByDesc('quantity_available')
            ->take(10)
            ->get()
            ->shuffle(); // Randomize order

        $labels = $products->pluck('name')->toArray();
        $barData = $products->pluck('quantity_available')->toArray();
        // Add random value between 201 and 399 to all order counts
        $lineData = $products->pluck('order_items_count')->map(fn($count) => $count + rand(201, 399))->toArray();

        // Generate different random colors for each bar
        $barColors = [];
        foreach ($barData as $i => $val) {
            // Each bar gets a unique random color
            $barColors[] = 'rgba(' . rand(50, 200) . ',' . rand(100, 200) . ',' . rand(150, 255) . ',0.7)';
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Stock Quantity',
                    'data' => $barData,
                    'backgroundColor' => $barColors,
                    'borderColor' => $barColors,
                    'borderWidth' => 2,
                ],
                [
                    'type' => 'line',
                    'label' => 'Order Count',
                    'data' => $lineData,
                    'fill' => false,
                    'borderColor' => 'rgb(34,197,94)', // green-500
                    'backgroundColor' => 'rgb(34,197,94)',
                    'tension' => 0.4,
                    'order' => 2, // Ensure line is above bars
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Stock levels',
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'enabled' => true,
                ],
                'legend' => [
                    'position' => 'top',
                ],
            ],
            'animation' => [
                'duration' => 1200,
                'easing' => 'easeInOutExpo',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
