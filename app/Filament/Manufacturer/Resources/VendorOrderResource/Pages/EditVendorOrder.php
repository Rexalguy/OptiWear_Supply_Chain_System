<?php

namespace App\Filament\Manufacturer\Resources\VendorOrderResource\Pages;

use App\Filament\Manufacturer\Resources\VendorOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVendorOrder extends EditRecord
{
    protected static string $resource = VendorOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
