<?php

namespace App\Filament\Vendor\Resources\VendorOrderResource\Pages;

use App\Filament\Vendor\Resources\VendorOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVendorOrders extends ListRecords
{
    protected static string $resource = VendorOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
