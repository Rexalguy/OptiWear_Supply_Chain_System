<?php

namespace App\Filament\Manufacturer\Resources\ProductionOrderResource\Pages;

use App\Filament\Manufacturer\Resources\ProductionOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductionOrder extends EditRecord
{
    protected static string $resource = ProductionOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
