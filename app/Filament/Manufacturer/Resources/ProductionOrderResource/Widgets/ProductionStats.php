<?php

namespace App\Filament\Manufacturer\Resources\ProductionOrderResource\Widgets;

use App\Models\Product;
use App\Models\Workforce;
use App\Models\ProductionOrder;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ProductionStats extends BaseWidget
{
        protected function getStats(): array
    {
        // Count available workers
        $availableWorkers = Workforce::count();

        // Count low stock products
        $lowStockCount = Product::where('quantity_available', '<', 200)->count();

        // Get biggest production order
        $biggestOrder = ProductionOrder::with('product')
            ->orderByDesc('quantity')
            ->first();

        return [
            Stat::make('Available Workers', $availableWorkers)
                ->icon('heroicon-o-user-group')
                ->description('Total workforce')
                ->color($availableWorkers > 7 ? 'success' : 'warning'),

            Stat::make('Low Stock Alerts', $lowStockCount)
                ->icon('heroicon-o-exclamation-triangle')
                ->description('Products below threshold')
                ->chart([20, 15, 17, 13, 9, 8, 5])
                ->color($lowStockCount > 0 ? 'danger' : 'success'),

            Stat::make('Biggest Order', $biggestOrder
                ? $biggestOrder->product->name . ' [' . $biggestOrder->quantity . ']'
                : 'No orders')
                ->icon('heroicon-o-chart-bar')
                ->description('Highest quantity ordered')
                ->chart([5, 8, 12, 27, 35, 40, 49])
                ->color('success'),
        ];
    }
}
