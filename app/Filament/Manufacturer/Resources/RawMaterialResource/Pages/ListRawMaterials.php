<?php

namespace App\Filament\Manufacturer\Resources\RawMaterialResource\Pages;

use App\Filament\Manufacturer\Resources\RawMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRawMaterials extends ListRecords
{
    protected static string $resource = RawMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
