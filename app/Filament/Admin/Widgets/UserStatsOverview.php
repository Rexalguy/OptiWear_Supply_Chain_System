<?php

namespace App\Filament\Admin\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $biggestCustomer = User::withCount('createdOrders')
            ->where('role', 'customer')
            ->orderByDesc('created_orders_count')
            ->first();

        $biggestVendor = User::withCount('createdVendorOrders')
            ->where('role', 'vendor')
            ->orderByDesc('created_vendor_orders_count')
            ->first();

        $customerWithMostTokens = User::where('role', 'customer')->orderByDesc('tokens')->first();

        return [
            Stat::make('Biggest Customer', $biggestCustomer?->name ?? 'N/A')
                ->description("Orders: " . ($biggestCustomer?->created_orders_count ?? 0))
                ->color('success')
                ->descriptionIcon('heroicon-m-user-group'),

            Stat::make('Biggest Vendor', $biggestVendor?->name ?? 'N/A')
                ->description("Vendor Orders: " . ($biggestVendor?->created_vendor_orders_count ?? 0))
                ->color('primary')
                ->descriptionIcon('heroicon-m-building-storefront'),

            Stat::make('Most Tokens', $customerWithMostTokens?->name ?? 'N/A')
                ->description('Tokens: ' . ($customerWithMostTokens->tokens ?? '0'))
                ->color('warning')
                ->descriptionIcon('heroicon-m-currency-dollar'),
        ];
    }
}
