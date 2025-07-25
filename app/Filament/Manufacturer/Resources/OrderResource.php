<?php

namespace App\Filament\Manufacturer\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Order;
use Filament\Forms\Form;
use App\Models\OrderItem;
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
use App\Filament\Manufacturer\Resources\OrderResource\Pages;
use App\Filament\Manufacturer\Resources\OrderResource\Pages\EditOrder;
use App\Filament\Manufacturer\Resources\OrderResource\Pages\ViewOrder;
use App\Filament\Manufacturer\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Manufacturer\Resources\OrderResource\RelationManagers;
use App\Filament\Manufacturer\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Manufacturer\Resources\OrderResource\Widgets\OrderStats;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $label = 'Customer Orders';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

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
               Forms\Components\Select::make('created_by')
                    ->label('Created By')
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('delivery_option')
                    ->required(),
                Forms\Components\Textarea::make('delivery_address')
                    ->label('Delivery Address')
                    ->disabled()
                    ->columnSpanFull()
                    ->visible(fn (?Order $record) => $record?->delivery_option === 'delivery')
                    ->afterStateHydrated(function ($state, callable $set, ?Order $record) {
                    if ($record && $record->delivery_option === 'delivery') {
                        $set('delivery_address', $record->customerInfo?->address ?? 'N/A');
                    }
                }),
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
            ->query(Order::with(['orderItems.product', 'creator', 'customerInfo'])->orderBy('id', 'desc'))
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
                TextColumn::make('expected_fulfillment_date')
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



                TextColumn::make('rating')
                    ->label('⭐ Rating')
                    ->formatStateUsing(fn($state) => $state ? str_repeat('⭐', $state) : '—')
                    ->visible(fn($record) => $record?->status === 'delivered'),
            ])
            ->filters([
                //
            ])
            ->actions([




                Action::make('markConfirmed')
                    ->label('Confirm')
                    ->color('info')
                    ->icon('heroicon-o-check-circle')
                    ->visible(fn(Order $record) => $record->status === 'pending')
                    ->action(function (Order $record, $livewire) {
                        foreach ($record->orderItems as $item) {
                            $product = $item->product;
                            if ($product->quantity_available < $item->quantity) {
                                $livewire->dispatch('sweetalert', [
                                    'title' => "Not enough stock for {$product->name}",
                                    'icon' => 'error',

                                ]);
                                return;
                            }
                        }

                        foreach ($record->orderItems as $item) {
                            $item->product->decrement('quantity_available', $item->quantity);
                        }

                        $record->update(['status' => 'confirmed']);

                        $livewire->dispatch('sweetalert', [
                            'title' => 'Order confirmed and stock reduced',
                            'icon' => 'success',

                        ]);
                    }),

                Action::make('markDelivered')
                    ->label('Deliver')
                    ->color('success')
                    ->icon('heroicon-o-truck')
                    ->visible(fn(Order $record) => $record->status === 'confirmed')
                    ->action(function (Order $record, $livewire) {
                        $record->update(['status' => 'delivered']);

                        if ($record->total >= 50000) {
                            $tokens = floor($record->total / 15000);
                            $record->creator->increment('tokens', $tokens);
                        }

                        $livewire->dispatch('sweetalert', [
                            'title' => 'Order marked as delivered and tokens awarded',
                            'icon' => 'success',

                        ]);
                    }),

                Action::make('markCancelled')
                    ->label('Cancel')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn(Order $record) => in_array($record->status, ['pending', 'confirmed']))
                    ->requiresConfirmation()
                    ->action(function (Order $record, $livewire) {
                        foreach ($record->orderItems as $item) {
                            $item->product->increment('quantity_available', $item->quantity);
                        }

                        $record->update(['status' => 'cancelled']);

                        $livewire->dispatch('sweetalert', [
                            'title' => 'Order cancelled and stock restored',
                            'icon' => 'error',

                        ]);
                    }),

                ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Action::make('viewItems')
                        ->label('View Items')
                        ->icon('heroicon-o-list-bullet')
                        ->color('gray')
                        ->modalHeading('Order Items')
                        ->infolist(function ($record) {
                            $items = OrderItem::with('product')
                                ->where('order_id', $record->id)
                                ->get();

                            if ($items->isEmpty()) {
                                return [
                                    Section::make()
                                        ->schema([
                                            TextEntry::make('none')
                                                ->default('No items found for this order.')
                                                ->color('danger')
                                                ->columnSpanFull(),
                                        ]),
                                ];
                            }

                            return [
                                Section::make('Items')
                                    ->schema([
                                        ...$items->map(function ($item) {
                                            return Grid::make(3)->schema([
                                                TextEntry::make('product')
                                                    ->label('Product')
                                                    ->default($item->product->name),

                                                TextEntry::make('quantity')
                                                    ->label('Quantity')
                                                    ->default($item->quantity),
                                            ]);
                                        })->toArray(),
                                        TextEntry::make('total')
                                            ->label('Total (UGX)')
                                            ->default('UGX ' . number_format($items->sum(function ($item) {
                                                return $item->quantity * $item->product->unit_price;
                                            }))),
                                    ]),
                            ];
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),

                    Action::make('viewReview')
                        ->label('Review')
                        ->color('gray')
                        ->icon('heroicon-o-eye')
                        ->visible(fn($record) =>  $record?->status === 'delivered')
                        ->modalHeading('Customer Review')
                        ->modalDescription(fn($record) => 'Order #' . $record->id)
                        ->modalContent(fn($record) => view('filament.manufacturer.pages.partials.view-review', ['record' => $record]))
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
            'index' => Pages\ListOrders::route('/'),
            // 'create' => Pages\CreateOrder::route('/create'),
            // 'view' => Pages\ViewOrder::route('/{record}'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }
}
