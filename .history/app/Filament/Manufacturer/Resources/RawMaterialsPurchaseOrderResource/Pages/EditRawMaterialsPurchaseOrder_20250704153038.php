<?php

namespace App\Filament\Resources\RawMaterialsPurchaseOrderResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\RawMaterialsPurchaseOrderResource;

class EditRawMaterialsPurchaseOrder extends EditRecord
{
    protected static string $resource = RawMaterialsPurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\EditAction::make()
            ->visible(fn ($record) => Auth::User()->role == 'manufacturer' && $record->created_by == Auth::id())
        ];
    }
}