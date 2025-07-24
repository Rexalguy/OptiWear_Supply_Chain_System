<?php

namespace App\Filament\Vendor\Resources;

use App\Filament\Vendor\Resources\VendorOrderResource\Pages;
use App\Filament\Vendor\Resources\VendorOrderResource\RelationManagers;
use App\Models\VendorOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorOrderResource extends Resource
{
    protected static ?string $model = VendorOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'View Orders';
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
            ->query(VendorOrder::with('items.product')->latest())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order #')
                    ->sortable(),
                Tables\Columns\TextColumn::make('delivery_option')
                    ->label('Delivery')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'pickup' => 'gray',
                        'delivery' => 'info',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Placed On')
                    ->dateTime()
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_fulfillment')
                    ->label('Expected Fulfillment')
                    ->getStateUsing(function ($record) {
                        if ($record->created_at->addDays(3) < now() || $record->status == 'cancelled') {
                            return 'Closed';
                        } elseif ($record->status == 'delivered') {
                            return 'Done';
                        }
                        return \Carbon\Carbon::parse($record->created_at->addDays(3))->diffForHumans([
                            'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                            'parts' => 2,
                        ]);
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('items')
                    ->label('Items')
                    ->html()
                    ->formatStateUsing(function ($state, $record) {
                        return $record->items
                            ->map(fn($item) => e($item->product->name) . " <small>(Qty: {$item->quantity})</small>")
                            ->implode('<br>');
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->label('Final Charged (UGX)')
                    ->formatStateUsing(fn($state) => 'UGX ' . number_format($state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->sortable()
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'confirmed',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->label('Order Status'),
                Tables\Filters\SelectFilter::make('delivery_option')
                    ->options([
                        'pickup' => 'Pickup',
                        'delivery' => 'Delivery',
                    ])
                    ->label('Delivery Method'),
            ])
            ->actions([
                Tables\Actions\Action::make('resume')
                    ->color('warning')
                    ->icon('heroicon-o-play')
                    ->label('Resume Order')
                    ->visible(fn(VendorOrder $record) => $record->status === 'cancelled')
                    ->requiresConfirmation()
                    ->action(function(VendorOrder $record) {
                        $record->update(['status' => 'pending']);
                        \Filament\Notifications\Notification::make()
                            ->title('Order Resumed Successfully')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->label('Cancel')
                    ->visible(fn(VendorOrder $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function($record) {
                        $record->update(['status' => 'cancelled']);
                        \Filament\Notifications\Notification::make()
                            ->title('Order Cancelled Successfully')
                            ->info()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make()
                    ->label('View Details')
                    ->visible(fn(VendorOrder $record) => in_array($record->status, ['pending', 'confirmed', 'delivered']))
            ])
            ->defaultSort('id', 'desc');
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
            // 'create' => Pages\CreateVendorOrder::route('/create'),
            // 'view' => Pages\ViewVendorOrder::route('/{record}'),
            // 'edit' => Pages\EditVendorOrder::route('/{record}/edit'),
        ];
    }
}