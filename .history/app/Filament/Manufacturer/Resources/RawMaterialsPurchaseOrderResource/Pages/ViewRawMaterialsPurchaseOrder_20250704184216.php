<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource;

class ViewRawMaterialsPurchaseOrder extends ViewRecord
{
    protected static string $resource = RawMaterialsPurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
            ->visible(fn ($record) => Auth::user()?->role === 'manufacturer'),
        ];
    }
}