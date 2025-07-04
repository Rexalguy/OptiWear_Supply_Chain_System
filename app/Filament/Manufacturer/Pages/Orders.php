<?php

namespace App\Filament\Manufacturer\Pages;

use App\Models\Order;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;

class Orders extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.manufacturer.pages.orders';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(Order::with(['orderItems.product', 'creator'])->orderBy('id', 'desc'))
            ->columns([
                TextColumn::make('id')
                    ->label('Order #')
                    ->sortable(),

                TextColumn::make('creator.name') 
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

                TextColumn::make('orderItems')
                    ->label('Items')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->orderItems->map(function ($item) {
                            return $item->product->name . ' (x' . $item->quantity . ')';
                        })->implode(', ');
                    })
                    ->wrap(),
            ])
            ->actions([
                Action::make('markConfirmed')
                    ->label('Confirm')
                    ->color('info')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->action(function (Order $record) {
                        // Reduce product quantities only if order is pending
                        foreach ($record->orderItems as $item) {
                            $product = $item->product;
                            // Check if enough quantity is available before reducing
                            if ($product->quantity_available < $item->quantity) {
                                Notification::make()
                                    ->title("Not enough stock for {$product->name}")
                                    ->danger()
                                    ->send();
                                return;
                            }
                        }
                        foreach ($record->orderItems as $item) {
                            $product = $item->product;
                            $product->decrement('quantity_available', $item->quantity);
                        }
                        $record->update(['status' => 'confirmed']);
                        Notification::make()
                            ->title('Order confirmed and stock reduced')
                            ->success()
                            ->send();
                    }),

                Action::make('markDelivered')
                    ->label('Deliver')
                    ->color('success')
                    ->icon('heroicon-o-truck')
                    ->visible(fn (Order $record) => $record->status === 'confirmed')
                    ->action(function (Order $record) {
                        $record->update(['status' => 'delivered']);
                        Notification::make()
                            ->title('Order marked as delivered')
                            ->success()
                            ->send();
                    }),

                Action::make('markCancelled')
                    ->label('Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Order $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        // Restore product quantities only if order is pending or confirmed
                        foreach ($record->orderItems as $item) {
                            $product = $item->product;
                            $product->increment('quantity_available', $item->quantity);
                        }
                        $record->update(['status' => 'cancelled']);
                        Notification::make()
                            ->title('Order cancelled and stock restored')
                            ->danger()
                            ->send();
                    }),
            ])
            ->defaultSort('id', 'desc');
    }
}
