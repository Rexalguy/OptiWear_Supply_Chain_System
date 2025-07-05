<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialCategoryResource\Pages;

use App\Filament\Manufacturer\Resources\RawMaterialCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRawMaterialCategory extends EditRecord
{
    protected static string $resource = RawMaterialCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
