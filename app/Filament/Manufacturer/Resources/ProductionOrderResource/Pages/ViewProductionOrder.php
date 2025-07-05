<?php

namespace App\Filament\Manufacturer\Resources\ProductionOrderResource\Pages;

use App\Filament\Manufacturer\Resources\ProductionOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProductionOrder extends ViewRecord
{
    protected static string $resource = ProductionOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
