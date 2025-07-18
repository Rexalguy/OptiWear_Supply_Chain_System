<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SegmentationTable extends BaseWidget
{
    protected static ?string $heading = 'Segment Recommendations';
    
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('segment')
                    ->label('Segment'),
                Tables\Columns\TextColumn::make('recommendation')
                    ->label('Recommendation'),
            ]);
    }

    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return null;
    }
}
