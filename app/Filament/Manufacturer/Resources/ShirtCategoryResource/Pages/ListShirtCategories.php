<?php

namespace App\Filament\Manufacturer\Resources\ShirtCategoryResource\Pages;

use App\Filament\Manufacturer\Resources\ShirtCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShirtCategories extends ListRecords
{
    protected static string $resource = ShirtCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
