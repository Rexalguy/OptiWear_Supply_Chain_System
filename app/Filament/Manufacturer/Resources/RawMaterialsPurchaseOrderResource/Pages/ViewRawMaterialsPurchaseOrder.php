<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource\Pages;

use App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRawMaterialsPurchaseOrder extends ViewRecord
{
    protected static string $resource = RawMaterialsPurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
