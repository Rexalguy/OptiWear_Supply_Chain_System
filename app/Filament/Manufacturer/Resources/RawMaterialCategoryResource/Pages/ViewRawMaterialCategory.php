<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialCategoryResource\Pages;

use App\Filament\Manufacturer\Resources\RawMaterialCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRawMaterialCategory extends ViewRecord
{
    protected static string $resource = RawMaterialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
