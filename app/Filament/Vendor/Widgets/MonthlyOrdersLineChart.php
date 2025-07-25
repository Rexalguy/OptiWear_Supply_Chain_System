<?php

namespace App\Filament\Vendor\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\VendorOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyOrdersLineChart extends ChartWidget
{
    protected static ?string $heading = 'Orders & Expenditure Trends';
    protected static ?string $description = 'Last 6 months of order activity';
    protected int $refreshInterval = 300; // Refresh every 5 minutes

    protected function getData(): array
    {
        $data = VendorOrder::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('SUM(total) as revenue')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [];
        $orderCounts = [];
        $expenditures = [];

        // Fill in any missing months with zeros
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = now()->subMonths($i)->format('Y-m');
            $monthData = $data->firstWhere('month', $monthKey);
            
            $labels[] = now()->subMonths($i)->format('M Y');
            $orderCounts[] = $monthData ? $monthData->order_count : 0;
            $revenues[] = $monthData ? round($monthData->revenue / 1000) : 0; // Convert to thousands
        }

        return [
            'datasets' => [
                [
                    'label' => 'Number of Orders',
                    'data' => $orderCounts,
                    'borderColor' => '#7c3aed', // Purple
                    'fill' => false,
                    'tension' => 0.3,
                ],
                [
                    'label' => 'Expenditure (Thousands UGX)',
                    'data' => $expenditures,
                    'borderColor' => '#10b981', // Green
                    'fill' => false,
                    'tension' => 0.3,
                    'yAxisID' => 'expenditure',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true,
                        'drawBorder' => true,
                    ],
                    'ticks' => [
                        'stepSize' => 5,
                        'padding' => 10,
                    ],
                    'position' => 'left',
                    'min' => 0,
                    'max' => 30, // Increased scale for orders
                ],
                'expenditure' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'stepSize' => 5000,
                        'padding' => 10,
                        'callback' => 'function(value) { return value.toLocaleString() + "K"; }',
                    ],
                    'min' => 0,
                    'max' => 100000, // Increased scale for expenditure
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }
}
