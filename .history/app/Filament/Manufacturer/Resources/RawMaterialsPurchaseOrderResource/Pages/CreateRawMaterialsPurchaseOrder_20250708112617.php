<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource;

class CreateRawMaterialsPurchaseOrder extends CreateRecord
{
    protected static string $resource = RawMaterialsPurchaseOrderResource::class;
   public function beforeCreate(): void
    {
        $this->record->order_date = now()--;
        $this->record->expected_delivery_date = now()->addDays(3);
        $this->record->created_by = Auth::id();
    }
}