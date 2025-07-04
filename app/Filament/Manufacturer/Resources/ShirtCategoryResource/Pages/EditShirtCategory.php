<?php

namespace App\Filament\Manufacturer\Resources\ShirtCategoryResource\Pages;

use App\Filament\Manufacturer\Resources\ShirtCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditShirtCategory extends EditRecord
{
    protected static string $resource = ShirtCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
