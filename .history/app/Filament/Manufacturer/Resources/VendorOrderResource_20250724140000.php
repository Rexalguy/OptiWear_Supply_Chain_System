<?php

namespace App\Filament\Manufacturer\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\VendorOrder;
use Filament\Forms\Form;
use App\Models\VendorOrderItem;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Manufacturer\Resources\VendorOrderResource\Pages;
use App\Filament\Manufacturer\Resources\VendorOrderResource\RelationManagers;

class VendorOrderResource extends Resource
{
    protected static ?string $model = VendorOrder::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    public static function canCreate(): bool
    {
        return false;
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
                Forms\Components\DateTimePicker::make('expected_fulfillment_date')
                    ->label('Expected Fulfillment Date')
                    ->required()
                    ->default(now()->addDays(7))
                    ->minDate(now())
                    ->maxDate(now()->addMonths(3))
                    ->displayFormat('d M Y '),
                Forms\Components\TextInput::make('rating')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('review')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(VendorOrder::with(['items.product', 'creator'])->orderBy('id', 'desc'))
            ->columns([
                TextColumn::make('id')
                    ->label('Order #')
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label('Vendor')
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
                    
                TextColumn::make('expected_fulfillment')
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
                    ->formatStateUsing(fn($state) => number_format($state, 0))
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Placed')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->since()
                    ->dateTooltip(),


            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('markConfirmed')
                    ->label('Confirm')
                    ->color('info')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn(VendorOrder $record) => $record->status === 'pending')
                    ->action(function (VendorOrder $record) {
                        foreach ($record->items as $item) {
                            $product = $item->product;
                            if ($product->quantity_available < $item->quantity) {
                                Notification::make()
                                    ->title("Not enough stock for {$product->name}")
                                    ->danger()
                                    ->send();
                                return;
                            }
                        }

                        foreach ($record->items as $item) {
                            $item->product->decrement('quantity_available', $item->quantity);
                        }

                        $record->update(['status' => 'confirmed']);

                        Notification::make()
                            ->title('Vendor order confirmed and stock reduced')
                            ->success()
                            ->send();
                    }),

                Action::make('markDelivered')
                    ->label('Deliver')
                    ->color('success')
                    ->icon('heroicon-o-truck')
                    ->visible(fn(VendorOrder $record) => $record->status === 'confirmed')
                    ->action(function (VendorOrder $record) {
                        $record->update(['status' => 'delivered']);

                        if ($record->total >= 50000) {
                            $tokens = floor($record->total / 15000);
                            $record->creator->increment('tokens', $tokens);
                        }

                        Notification::make()
                            ->title('Vendor order marked as delivered and tokens awarded')
                            ->success()
                            ->send();
                    }),

                Action::make('markCancelled')
                    ->label('Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn(VendorOrder $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->requiresConfirmation()
                    ->action(function (VendorOrder $record) {
                        foreach ($record->items as $item) {
                            $item->product->increment('quantity_available', $item->quantity);
                        }

                        $record->update(['status' => 'cancelled']);

                        Notification::make()
                            ->title('Vendor order cancelled and stock restored')
                            ->danger()
                            ->send();
                    }),

                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Action::make('viewItems')
                        ->label('View Items')
                        ->icon('heroicon-o-list-bullet')
                        ->color('gray')
                        ->modalHeading('Vendor Order Items')
                        ->infolist(function ($record) {
                            $items = VendorOrderItem::with('product')
                                ->where('vendor_order_id', $record->id)
                                ->get();

                            if ($items->isEmpty()) {
                                return [
                                    Section::make()
                                        ->schema([
                                            TextEntry::make('none')
                                                ->default('No items found for this vendor order.')
                                                ->color('danger')
                                                ->columnSpanFull(),
                                        ]),
                                ];
                            }

                            return [
                                Section::make('Items')
                                    ->schema(
                                        $items->map(function ($item) {
                                            return Grid::make(3)->schema([
                                                TextEntry::make('product')
                                                    ->label('Product')
                                                    ->default($item->product->name),

                                                TextEntry::make('quantity')
                                                    ->label('Quantity')
                                                    ->default($item->quantity),

                                                TextEntry::make('total')
                                                    ->label('Total (UGX)')
                                                    ->default('UGX ' . number_format($item->quantity * $item->product->price)),
                                            ]);
                                        })->toArray()
                                    ),
                            ];
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),

                    Action::make('viewReview')
                        ->label('Review')
                        ->color('gray')
                        ->icon('heroicon-o-eye')
                        ->visible(fn($record) => $record?->status === 'delivered')
                        ->modalHeading('Vendor Review')
                        ->modalDescription(fn($record) => 'Vendor Order #' . $record->id)
                        ->modalContent(fn($record) => view('filament.manufacturer.pages.partials.view-vendor-review', ['record' => $record]))
                        ->modalSubmitAction(false),
                ])->iconButton()
                ->color('info')
                ->tooltip('Details'),
            ])
            ->defaultSort('id', 'desc')
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
            // 'create' => Pages\CreateVendorOrder::route('/create'),
            // 'view' => Pages\ViewVendorOrder::route('/{record}'),
            // 'edit' => Pages\EditVendorOrder::route('/{record}/edit'),
        ];
    }
}
