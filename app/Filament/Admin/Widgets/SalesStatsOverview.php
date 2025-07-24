<?php

namespace App\Filament\Admin\Widgets;

use Carbon\Carbon;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class SalesStatsOverview extends BaseWidget
{


    protected function getStats(): array
    {
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();

        $ordersToday = Order::whereDate('created_at', $today)->count();
        $ordersThisWeek = Order::whereBetween('created_at', [$startOfWeek, now()])->count();
        $ordersThisMonth = Order::whereBetween('created_at', [$startOfMonth, now()])->count();
        $totalOrders = Order::count();

        $daysSinceFirstOrder = Order::min('created_at')
            ? Carbon::parse(Order::min('created_at'))->diffInDays(now()) + 1
            : 1;

        $averageOrders = $daysSinceFirstOrder > 0 ? round($totalOrders / $daysSinceFirstOrder, 2) : 0;

        return [
            Stat::make('🟢 Orders Today', $ordersToday)
                ->color('success')
                ->description("Today: {$ordersToday}")
                ->descriptionIcon('heroicon-m-calendar-date-range'),

            Stat::make('📆 Orders This Week', $ordersThisWeek)
                ->color('primary')
                ->description("Since " . $startOfWeek->format('D'))
                ->descriptionIcon('heroicon-m-calendar-date-range'),

            Stat::make('📅 Orders This Month', $ordersThisMonth)
                ->color('warning')
                ->description("Since " . $startOfMonth->format('M d'))
                ->descriptionIcon('heroicon-m-calendar-date-range'),



            Stat::make('📊 Avg Orders/Day', $averageOrders)
                ->color('info')
                ->description("Across {$daysSinceFirstOrder} days")
                ->descriptionIcon('heroicon-m-chart-bar'),
        ];
    }
}
