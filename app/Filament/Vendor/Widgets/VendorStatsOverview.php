<?php

namespace App\Filament\Vendor\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Product;
use App\Models\VendorOrderItem;
use App\Models\VendorOrder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorStatsOverview extends BaseWidget
{
    protected int $sortBy = 1;

    protected function getStats(): array
    {
        // Get latest products count (last 30 days)
        $latestProductsCount = Product::where('created_at', '>=', now()->subDays(30))->count();
        $previousMonthCount = Product::whereBetween('created_at', [
            now()->subDays(60), 
            now()->subDays(30)
        ])->count();
        $productChange = $previousMonthCount > 0 
            ? round((($latestProductsCount - $previousMonthCount) / $previousMonthCount) * 100)
            : 0;

        // Get top ordered item
        $topProduct = VendorOrderItem::select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->first();

        // Calculate monthly revenue
        $monthlyRevenue = VendorOrder::where('created_at', '>=', now()->startOfMonth())
            ->sum('total');
        $lastMonthRevenue = VendorOrder::whereBetween('created_at', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ])->sum('total');
        $revenueChange = $lastMonthRevenue > 0 
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100)
            : 0;

        // Calculate order fulfillment rate
        $totalOrders = VendorOrder::where('created_at', '>=', now()->subDays(30))->count();
        $fulfilledOrders = VendorOrder::where('created_at', '>=', now()->subDays(30))
            ->where('status', 'completed')
            ->count();
        $fulfillmentRate = $totalOrders > 0 ? round(($fulfilledOrders / $totalOrders) * 100) : 0;

        return [
            Stat::make('New Products (30 days)', (string)$latestProductsCount)
                ->description($productChange . '% vs last month')
                ->descriptionIcon($productChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($productChange >= 0 ? 'success' : 'danger')
                ->icon('heroicon-o-cube-transparent'),

            Stat::make('Top Ordered Item', $topProduct ? $topProduct->product->name : 'No orders yet')
                ->description($topProduct ? number_format($topProduct->total_quantity) . ' units ordered' : '')
                ->icon('heroicon-o-trophy')
                ->color('warning'),

            Stat::make('Monthly Expenditure', 'UGX ' . number_format($monthlyRevenue))
                ->description($revenueChange . '% vs last month')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([
                    VendorOrder::where('created_at', '>=', now()->subDays(7))->sum('total'),
                    VendorOrder::where('created_at', '>=', now()->subDays(6))->sum('total'),
                    VendorOrder::where('created_at', '>=', now()->subDays(5))->sum('total'),
                    VendorOrder::where('created_at', '>=', now()->subDays(4))->sum('total'),
                    VendorOrder::where('created_at', '>=', now()->subDays(3))->sum('total'),
                    VendorOrder::where('created_at', '>=', now()->subDays(2))->sum('total'),
                    VendorOrder::where('created_at', '>=', now()->subDays(1))->sum('total'),
                ])
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make('Order Fulfillment Rate', $fulfillmentRate . '%')
                ->description($totalOrders . ' total orders this month')
                ->icon('heroicon-o-truck')
                ->color($fulfillmentRate >= 80 ? 'success' : ($fulfillmentRate >= 60 ? 'warning' : 'danger')),
        ];
    }
}
