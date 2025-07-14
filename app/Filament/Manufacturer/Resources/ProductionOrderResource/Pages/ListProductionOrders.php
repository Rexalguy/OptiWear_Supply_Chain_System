<?php

namespace App\Filament\Manufacturer\Resources\ProductionOrderResource\Pages;

use Filament\Actions;
use App\Models\ProductionOrder;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Manufacturer\Resources\ProductionOrderResource;
use App\Filament\Manufacturer\Resources\ProductionOrderResource\Widgets\ProductionStats;

class ListProductionOrders extends ListRecords
{
    protected static string $resource = ProductionOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    
    protected function getHeaderWidgets(): array
    {
        return [
            ProductionStats::class,
        ];
    }

    public  function getTabs(): array
{
    return [
        'all' => Tab::make('All')
            ->badge(fn () => ProductionOrder::count()),

        'pending' => Tab::make('Pending')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
            ->badge(fn () => ProductionOrder::where('status', 'pending')->count())
            ->badgeColor('warning'),

        'in_progress' => Tab::make('In Progress')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'in_progress'))
            ->badge(fn () => ProductionOrder::where('status', 'in_progress')->count())
            ->badgeColor('info'),

        'completed' => Tab::make('Completed')
            ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
            ->badge(fn () => ProductionOrder::where('status', 'completed')->count())
            ->badgeColor('success'),
    ];
}
}
