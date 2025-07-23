<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource;

class CreateRawMaterialsPurchaseOrder extends CreateRecord
{
    protected static string $resource = RawMaterialsPurchaseOrderResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['expected_delivery_date'] = now()->addDays(3);
        $data['created_by'] = Auth::id();
        $data['status'] = 'pending';

        return $data;
    }
}