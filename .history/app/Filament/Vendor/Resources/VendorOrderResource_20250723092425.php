<?php

namespace App\Filament\Vendor\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\VendorOrder;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use App\Filament\Vendor\Resources\VendorOrderResource\Pages;

class VendorOrderResource extends Resource
{
    protected static ?string $model = VendorOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Track Orders';
    protected static ?string $navigationGroup = 'Orders';
    public static function canCreate(): bool
    {
        return false; // Disable creation from the navigation
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('created_by')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('delivery_option')
                    ->required(),
                Forms\Components\TextInput::make('total')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('order_date')
                    ->label('Order Date')
                    ->required(),
                Forms\Components\DateTimePicker::make('expected_fulfillment')
                    ->label('Expected Fulfillment')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('delivery_option'),
                Tables\Columns\TextColumn::make('total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('order_date')
                    ->label('Order Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_fulfillment')
                    ->label('Expected Fulfillment')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('items')
                    ->label('Products Ordered')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->items
                            ->map(fn($item) => e($item->product->name) . " <small>(Qty: {$item->quantity})</small>")
                            ->implode('<br>');
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('CancelOrder')
                    ->label('Cancel Order')
                    ->requiresConfirmation()
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn($record) => $record->status === 'pending')
                    ->action(function ($record, $livewire) {
                        $record->update(['status' => 'cancelled']);
                        $livewire->dispatch('cart-updated', [
                            'title' => 'Order cancelled successfully!',
                            'icon' => 'warning',
                            'iconColor' => 'red',
                        ]);
                    }),
                Action::make('ResumeOrder')
                    ->label('Resume Order')
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-play-circle')
                    ->visible( fn($record) => $record->status === 'cancelled')
                    ->action(function ($record, $livewire) {
                        $record->update(['status' => 'pending']);
                        $livewire->dispatch('cart-updated', [
                            'title' => 'Order Resumed successfully!',
                            'icon' => 'info',
                            'iconColor' => 'blue',
                        ]);
                    }),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVendorOrders::route('/'),
        ];
    }
}