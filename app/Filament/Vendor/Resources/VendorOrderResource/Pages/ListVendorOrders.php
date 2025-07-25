<?php

namespace App\Filament\Vendor\Resources\VendorOrderResource\Pages;

use App\Filament\Vendor\Resources\VendorOrderResource;
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

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->icon('heroicon-m-list-bullet')
                ->badgeColor('primary')
                ->badge(VendorOrderResource::getModel()::count()),
            'pending' => Tab::make()
                ->icon('heroicon-m-clock')
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(VendorOrderResource::getModel()::where('status', 'pending')->count()),
            'confirmed' => Tab::make()
                ->icon('heroicon-m-check-circle')
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed'))
                ->badge(VendorOrderResource::getModel()::where('status', 'confirmed')->count()),
            'delivered' => Tab::make()
                ->icon('heroicon-m-truck')
                ->badgeColor('info')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'delivered'))
                ->badge(VendorOrderResource::getModel()::where('status', 'delivered')->count()),
            'cancelled' => Tab::make()
                ->icon('heroicon-m-x-circle')
                ->badgeColor('danger')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled'))
                ->badge(VendorOrderResource::getModel()::where('status', 'cancelled')->count()),
        ];
    }
}
