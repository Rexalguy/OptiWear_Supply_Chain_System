<?php

namespace App\Filament\Resources\RawMaterialsPurchaseOrderResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\RawMaterialsPurchaseOrderResource;

class ViewRawMaterialsPurchaseOrder extends ViewRecord
{
    protected static string $resource = RawMaterialsPurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
            ->visible(fn ($record) => $record->status == 'pending' && Auth::user()?->role == 'manufacturer'),
        ];
    }
}