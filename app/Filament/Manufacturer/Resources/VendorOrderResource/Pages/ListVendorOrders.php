<?php

namespace App\Filament\Manufacturer\Resources\VendorOrderResource\Pages;

use App\Filament\Manufacturer\Resources\VendorOrderResource;
use App\Models\VendorOrder;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListVendorOrders extends ListRecords
{
    protected static string $resource = VendorOrderResource::class;

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
            ->badge(VendorOrder::where('status', 'pending')->count())
            ->badgeColor('warning')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending')),

        'confirmed' => Tab::make('Confirmed')
            ->badge(VendorOrder::where('status', 'confirmed')->count())
            ->badgeColor('info')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed')),

        'delivered' => Tab::make('Delivered')
            ->badge(VendorOrder::where('status', 'delivered')->count())
            ->badgeColor('success')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'delivered')),

        'cancelled' => Tab::make('Cancelled')
            ->badge(VendorOrder::where('status', 'cancelled')->count())
            ->badgeColor('danger')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled')),
    ];

    }
}
