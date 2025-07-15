<?php

namespace App\Filament\Manufacturer\Resources\ManufacturerOrderResource\Pages;

use App\Filament\Manufacturer\Resources\ManufacturerOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManufacturerOrder extends EditRecord
{
    protected static string $resource = ManufacturerOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
