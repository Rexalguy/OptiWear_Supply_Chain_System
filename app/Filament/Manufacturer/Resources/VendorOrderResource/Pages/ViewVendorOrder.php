<?php

namespace App\Filament\Manufacturer\Resources\VendorOrderResource\Pages;

use App\Filament\Manufacturer\Resources\VendorOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewVendorOrder extends ViewRecord
{
    protected static string $resource = VendorOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
