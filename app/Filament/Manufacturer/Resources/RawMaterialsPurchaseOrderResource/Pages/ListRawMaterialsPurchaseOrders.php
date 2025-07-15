<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use App\Models\RawMaterialsPurchaseOrder;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource;

class ListRawMaterialsPurchaseOrders extends ListRecords
{
    protected static string $resource = RawMaterialsPurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
{
    return [
        'all' => Tab::make(),

        'pending' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
            ->badge(fn () => RawMaterialsPurchaseOrder::where('status', 'pending')->count())
            ->badgeColor('warning'),

        'confirmed' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'confirmed'))
            ->badge(fn () => RawMaterialsPurchaseOrder::where('status', 'confirmed')->count())
            ->badgeColor('info'),

        'delivered' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'delivered'))
            ->badge(fn () => RawMaterialsPurchaseOrder::where('status', 'delivered')->count())
            ->badgeColor('success'),

        'cancelled' => Tab::make()
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled'))
            ->badge(fn () => RawMaterialsPurchaseOrder::where('status', 'cancelled')->count())
            ->badgeColor('danger'),
    ];
}


}
