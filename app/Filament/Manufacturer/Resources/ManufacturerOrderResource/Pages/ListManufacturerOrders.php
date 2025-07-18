<?php

namespace App\Filament\Manufacturer\Resources\ManufacturerOrderResource\Pages;

use App\Filament\Manufacturer\Resources\ManufacturerOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManufacturerOrders extends ListRecords
{
    protected static string $resource = ManufacturerOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
