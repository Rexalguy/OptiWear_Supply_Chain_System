<?php

namespace App\Filament\Customer\Widgets;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class MyStatsWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    

    protected function getStats(): array
    {
        $user = Auth::user();

        $tokenCount = $user?->tokens ?? 0;

        $pendingOrders = Order::where('created_by', $user->id)
            ->where('status', 'pending')
            ->count();

        $confirmedOrders = Order::where('created_by', $user->id)
            ->where('status', 'confirmed')
            ->count();

        return [
            Stat::make('Your Tokens', $tokenCount)
                ->description('Tokens Earned')
                ->color('info'),

            Stat::make('Pending Orders', $pendingOrders)
                ->description('Waiting for processing')
                ->color('warning')
                ->icon('heroicon-o-arrow-path'),

            Stat::make('Confirmed Orders', $confirmedOrders)
                ->description('Ready for Acquisition')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
        ];
    }
}