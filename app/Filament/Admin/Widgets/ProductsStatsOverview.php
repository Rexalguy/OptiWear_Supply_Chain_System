<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ProductsStatsOverview extends BaseWidget
{
        protected static ?string $pollingInterval = '60s';

    protected function getStats(): array
    {
        return [
            // 🧮 Total Products in Stock
            Stat::make('Total Products in Stock', number_format(Product::sum('quantity_available')))
                ->description('Current stock across all products')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            // ⭐ Most Ordered Product
            Stat::make('Most Ordered Product', $this->getTopProductName())
                ->description('Based on total orders')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),

            // 🚫 Out of Stock Products
            Stat::make('Out of Stock Products', Product::where('quantity_available', 0)->count())
                ->description('Products that need restocking')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }

    private function getTopProductName(): string
    {
        $topProduct = OrderItem::selectRaw('product_id, COUNT(*) as total')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->first();

        return Product::find($topProduct?->product_id)?->name ?? 'N/A';
    }
}
