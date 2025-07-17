<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

   public function getStats(): array
{
    // Count vendors and customers
    $vendorCount = User::where('role', 'vendor')->count();

    $customerCount = User::where('role', 'customer')->count();


    // Order stats
    $ordersThisMonth = Order::whereBetween('created_at', [now()->startOfMonth(), now()])->count();
    $ordersLastMonth = Order::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();
    $ordersTrend = $ordersLastMonth > 0 ? (($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100 : null;

    // Low stock count
    $lowStockCount = Product::where('quantity_available', '<', 200)->count();

    // Monthly order chart
        $monthlyOrdersRaw = Order::selectRaw('COUNT(*) as total, DATE_FORMAT(created_at, "%b") as month, MONTH(created_at) as month_number')
        ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
        ->groupBy('month', 'month_number')
        ->orderBy('month_number')
        ->get();

    $monthlyOrders = $monthlyOrdersRaw->pluck('total', 'month')->toArray();
    $monthlyTotals = array_values($monthlyOrders);

    // Calculate trend: compare this month vs average of last 5 months
    $thisMonthOrders = $monthlyTotals[count($monthlyTotals) - 1] ?? 0;
    $previousMonths = array_slice($monthlyTotals, 0, -1);
    $averagePrevious = count($previousMonths) ? array_sum($previousMonths) / count($previousMonths) : 0;

    $ordersTrend = $averagePrevious > 0 ? (($thisMonthOrders - $averagePrevious) / $averagePrevious) * 100 : null;

    return [
        Stat::make('🧑‍💼 Vendors', number_format($vendorCount))
            ->description('Registered vendors')
            ->descriptionIcon('heroicon-m-user-group')
            ->color('success'),

        Stat::make('👥 Customers', number_format($customerCount))
            ->description('Total customers')
            ->descriptionIcon('heroicon-m-users')
            ->color('info'),

        Stat::make('🧾 Orders (This Month)', number_format($thisMonthOrders))
            ->description($ordersTrend !== null ? abs(round($ordersTrend, 1)) . '%' . ($ordersTrend >= 0 ? ' increase' : ' decrease') : 'No past data')
            ->descriptionIcon($ordersTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
            ->color($ordersTrend >= 0 ? 'success' : 'danger')
            ->chart($monthlyTotals),

        Stat::make('📦 Low Stock', $lowStockCount)
            ->description('Below threshold')
            ->descriptionIcon('heroicon-m-exclamation-triangle')
            ->color('danger'),
    ];
}
}
