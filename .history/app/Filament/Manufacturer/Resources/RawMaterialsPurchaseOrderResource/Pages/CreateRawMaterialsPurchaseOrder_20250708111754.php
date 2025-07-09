<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource\Pages;

use App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRawMaterialsPurchaseOrder extends CreateRecord
{
    protected static string $resource = RawMaterialsPurchaseOrderResource::class;
   public function beforeCreate(): void
    {
        $this->record->order_date = now();
        $this->record->expected_delivery_date = now()->addDays(3);
        $record-
    }
}