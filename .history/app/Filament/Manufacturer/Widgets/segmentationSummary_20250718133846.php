<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class segmentationSummary extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                //
            ]);
    }

    protected function getTableQuery()
    {
        // Return empty query for now
        return \App\Models\User::query()->whereRaw('1 = 0');
    }
}