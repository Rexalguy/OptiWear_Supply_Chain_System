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
use Carbon\Carbon;

class Orders extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'filament.manufacturer.pages.orders';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

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
                TextColumn::make('expected_delivery_date')
    ->label('Expected Delivery Date')
    ->formatStateUsing(function ($state, $record) {
        if ($record->status === 'delivered') {
            return 'Done';
        }

        if (empty($state)) {
            return 'N/A';
        }

        try {
            return Carbon::parse($state)->format('d M Y H:i');
        } catch (\Exception $e) {
            return 'Invalid Date';
        }
    })
    ->sortable(),

                TextColumn::make('total')
                    ->label('Total (UGX)')
                    ->formatStateUsing(fn ($state) => number_format($state, 0))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Placed On')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                TextColumn::make('orderItems')
                    ->label('Items')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->orderItems->map(function ($item) {
                            return $item->product->name . ' (x' . $item->quantity . ' ' . $item->product->sku . ')';
                        })->implode(', ');
                    })
                    ->wrap(),

                TextColumn::make('rating')
                    ->label('⭐ Rating')
                    ->formatStateUsing(fn ($state) => $state ? str_repeat('⭐', $state) : '—')
                    ->visible(fn ($record) => $record?->status === 'delivered'),

            ])
            ->actions([
               Action::make('viewReview')
    ->label('View Review')
    ->color('gray')
    ->icon('heroicon-o-eye')
    ->visible(fn ($record) => !empty($record?->review) && $record?->status === 'delivered')
    ->modalHeading('Customer Review')
    ->modalDescription(fn ($record) => 'Order #' . $record->id)
    ->modalContent(fn ($record) => view('filament.manufacturer.pages.partials.view-review', ['record' => $record]))
    ->modalSubmitAction(false),

                Action::make('markConfirmed')
                    ->label('Confirm')
                    ->color('info')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->action(function (Order $record) {
                        foreach ($record->orderItems as $item) {
                            $product = $item->product;
                            if ($product->quantity_available < $item->quantity) {
                                Notification::make()
                                    ->title("Not enough stock for {$product->name}")
                                    ->danger()
                                    ->send();
                                return;
                            }
                        }

                        foreach ($record->orderItems as $item) {
                            $item->product->decrement('quantity_available', $item->quantity);
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
                    ->action(fn (Order $record) => $record->update(['status' => 'delivered'])),

                Action::make('markCancelled')
                    ->label('Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn (Order $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        foreach ($record->orderItems as $item) {
                            $item->product->increment('quantity_available', $item->quantity);
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