<?php

namespace App\Filament\Vendor\Resources\VendorOrderResource\Pages;

use App\Filament\Vendor\Resources\VendorOrderResource;
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
