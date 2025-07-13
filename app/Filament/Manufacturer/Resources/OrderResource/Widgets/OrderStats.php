<?php

namespace App\Filament\Manufacturer\Resources\OrderResource\Widgets;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class OrderStats extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';

     protected function getStats(): array
    {
        // 🔥 Most ordered product
        $topProduct = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->with('product')
            ->first();

        // 💰 Total confirmed revenue
        $totalRevenue = Order::where('status', 'delivered')->sum('total');

        // 🧱 Largest single order
        $largestOrder = Order::orderByDesc('total')->first();

        return [
            Stat::make('Most Ordered Product', $topProduct?->product->name ?? '—')
                ->description("Total: " . ($topProduct?->total_quantity ?? 0))
                ->color('primary'),

            Stat::make('Confirmed Revenue', 'UGX ' . number_format($totalRevenue))
                ->description('All confirmed orders')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),

            Stat::make('Largest Order', $largestOrder ? 'UGX ' . number_format($largestOrder->total) : '—')
                ->description('Order #' . ($largestOrder?->id ?? '—'))
                ->color('info'),
        ];
    }
}
