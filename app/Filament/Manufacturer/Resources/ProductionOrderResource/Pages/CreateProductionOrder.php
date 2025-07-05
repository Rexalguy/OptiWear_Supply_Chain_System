<?php

namespace App\Filament\Manufacturer\Resources\ProductionOrderResource\Pages;

use App\Filament\Manufacturer\Resources\ProductionOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProductionOrder extends CreateRecord
{
    protected static string $resource = ProductionOrderResource::class;
}
