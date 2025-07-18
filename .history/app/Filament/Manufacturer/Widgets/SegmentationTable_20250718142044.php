<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SegmentationTable extends BaseWidget
{
    protected static ?string $heading = 'Segment Details';
    
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('column1')
                    ->label('Column 1'),
                Tables\Columns\TextColumn::make('column2')
                    ->label('Column 2'),
            ]);
    }

    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        // Return null for empty table
        return null;
    }
}
