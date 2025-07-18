<?php

namespace App\Filament\Admin\Widgets;


use App\Models\Product;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStock extends BaseWidget
{

    protected int | string | array $columnSpan = 3;

    protected int | string | array $maxHeight = '300px';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::with('shirtCategory')
                    ->where('quantity_available', '<', 200)
                    ->orderBy('quantity_available', 'asc')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Product')
                    ->sortable()
                    ->description(fn ($record) => $record->ShirtCategory?->category ?? 'Uncategorized'),

                TextColumn::make('quantity_available')
                    ->label('Qty Left')
                    ->badge()
                    ->color(fn ($state) => $state < 5 ? 'danger' : 'warning')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Added On')
                    ->date('M d, Y')
            ]);
    }

}

