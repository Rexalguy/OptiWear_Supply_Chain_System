<?php

namespace App\Filament\Vendor\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\VendorOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductRadar extends ChartWidget
{
    protected static ?string $heading = 'Monthly Top Products Distribution';
    protected static ?string $description = 'Distribution of top 5 products purchased per month';
    protected int $refreshInterval = 300; // Refresh every 5 minutes


    protected int | string | array $columnSpan = 'full';

    protected static ?string $height = '700px';

    protected function getData(): array
    {
        // Get the last 6 months of data
        $startDate = Carbon::now()->subMonths(6)->startOfMonth();
        
        // First, get the top 5 products by total quantity
        $topProducts = VendorOrderItem::join('products', 'vendor_order_items.product_id', '=', 'products.id')
            ->select('products.id', 'products.name')
            ->selectRaw('SUM(vendor_order_items.quantity) as total_quantity')
            ->where('vendor_order_items.created_at', '>=', $startDate)
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Get monthly data for these products
        $monthlyData = VendorOrderItem::join('products', 'vendor_order_items.product_id', '=', 'products.id')
            ->whereIn('products.id', $topProducts->pluck('id'))
            ->where('vendor_order_items.created_at', '>=', $startDate)
            ->select(
                'products.name',
                DB::raw('DATE_FORMAT(vendor_order_items.created_at, "%Y-%m") as month'),
                DB::raw('SUM(vendor_order_items.quantity) as quantity')
            )
            ->groupBy('products.name', 'month')
            ->orderBy('month')
            ->get();

        // Prepare datasets
        $months = $monthlyData->pluck('month')->unique()->values();
        $datasets = [];
        $colors = [
            ['backgroundColor' => 'rgba(59, 130, 246, 0.2)', 'borderColor' => '#3b82f6'], // blue
            ['backgroundColor' => 'rgba(239, 68, 68, 0.2)', 'borderColor' => '#ef4444'], // red
            ['backgroundColor' => 'rgba(16, 185, 129, 0.2)', 'borderColor' => '#10b981'], // green
            ['backgroundColor' => 'rgba(245, 158, 11, 0.2)', 'borderColor' => '#f59e0b'], // yellow
            ['backgroundColor' => 'rgba(139, 92, 246, 0.2)', 'borderColor' => '#8b5cf6'], // purple
            ['backgroundColor' => 'rgba(236, 72, 153, 0.2)', 'borderColor' => '#ec4899'], // pink
        ];

        foreach ($months as $index => $month) {
            $monthData = $monthlyData->where('month', $month);
            $datasets[] = [
                'label' => Carbon::createFromFormat('Y-m', $month)->format('F Y'),
                'data' => $topProducts->map(function ($product) use ($monthData) {
                    return $monthData->where('name', $product->name)->first()?->quantity ?? 0;
                })->toArray(),
                'backgroundColor' => $colors[$index % count($colors)]['backgroundColor'],
                'borderColor' => $colors[$index % count($colors)]['borderColor'],
                'borderWidth' => 2,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $topProducts->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'radar';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                   
                    'angleLines' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'display' => false,
                        'stepSize' => 50,
                    ],
                    'pointLabels' => [
                        'font' => [
                            'size' => 14,
                            'weight' => 'bold',
                        ],
                        'padding' => 20,
                    ],
                ],

                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'padding' => 20,
                    ],
                ],

            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }

    protected function getHeight(): ?string
    {
        return '400px';
    }
}
