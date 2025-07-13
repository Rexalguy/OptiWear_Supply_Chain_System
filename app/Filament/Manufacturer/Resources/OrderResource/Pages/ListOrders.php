<?php

namespace App\Filament\Manufacturer\Resources\OrderResource\Pages;

use App\Models\Order;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Manufacturer\Resources\OrderResource;
use App\Filament\Manufacturer\Resources\OrderResource\Widgets\OrderStats;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public  function getTabs(): array
{
    return [
        'all' => Tab::make('All'),

        'pending' => Tab::make('Pending')
            ->badge(Order::where('status', 'pending')->count())
            ->badgeColor('warning')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

        'confirmed' => Tab::make('Confirmed')
            ->badge(Order::where('status', 'confirmed')->count())
            ->badgeColor('info')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed')),

        'delivered' => Tab::make('Delivered')
            ->badge(Order::where('status', 'delivered')->count())
            ->badgeColor('success')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'delivered')),

        'cancelled' => Tab::make('Cancelled')
            ->badge(Order::where('status', 'cancelled')->count())
            ->badgeColor('danger')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
    ];

    }

    public  function getHeaderWidgets(): array
{
    return [
        OrderStats::class,
    ];
}
}
