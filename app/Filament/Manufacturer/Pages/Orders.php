<?php

namespace App\Filament\Manufacturer\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;

class Orders extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.manufacturer.pages.orders';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(Order::with(['items.product', 'creator'])->orderBy('id', 'desc'))
            ->columns([
                TextColumn::make('id')
                    ->label('Order #')
                    ->sortable(),

                TextColumn::make('creator.name') // ✅ fixed relationship name
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->default('Unknown'),

                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ]),

                TextColumn::make('delivery_option')
                    ->label('Delivery')
                    ->sortable(),

                TextColumn::make('total')
                    ->money('UGX', true)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Placed On')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('items_summary')
                    ->label('Items')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->items->map(function ($item) {
                            return $item->product->name . ' (x' . $item->quantity . ')';
                        })->implode(', ');
                    })
                    ->wrap(),
            ])
            ->defaultSort('id', 'desc');
    }
}
