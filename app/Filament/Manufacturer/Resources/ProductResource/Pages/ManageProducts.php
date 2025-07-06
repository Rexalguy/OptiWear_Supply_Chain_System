<?php

namespace App\Filament\Manufacturer\Resources\ProductResource\Pages;

use App\Filament\Manufacturer\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProducts extends ManageRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
