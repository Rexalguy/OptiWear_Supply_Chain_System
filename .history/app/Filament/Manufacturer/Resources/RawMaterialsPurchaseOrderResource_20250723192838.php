<?php

namespace App\Filament\Manufacturer\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RawMaterial;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\RawMaterialsPurchaseOrder;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource\Pages;
use App\Filament\Manufacturer\Resources\RawMaterialsPurchaseOrderResource\RelationManagers;

class RawMaterialsPurchaseOrderResource extends Resource
{
    protected static ?string $model = RawMaterialsPurchaseOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationGroup = 'Raw Materials';

    public static function getNavigationBadge(): ?string
    {
        return RawMaterialsPurchaseOrder::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return RawMaterialsPurchaseOrder::where('status', 'pending')->count() > 5 ? 'warning' : 'info';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pending Raw Material Orders';
    }

    public static function canViewAny(): bool
    {
        return Auth::user()?->role == 'manufacturer' || Auth::user()?->role == 'supplier';
    }
    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()?->role === 'manufacturer' || Auth::user()?->role === 'supplier';
    }
    public static function canCreate(): bool
    {
        return Auth::user()?->role === 'manufacturer';
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('created_at');
    }

public static function form(Form $form): Form
{
    return $form->schema([
        Forms\Components\Wizard::make([
            Forms\Components\Wizard\Step::make('Order')
                ->description('Choose the raw material and supplier')
                ->icon('heroicon-o-cube')
                ->schema([
                    Select::make('raw_materials_id')
                        ->label('Raw Material')
                        ->options(RawMaterial::all()->pluck('name', 'id'))
                        ->required()
                        ->live()
                        ->searchable()
                        ->preload()
                        ->native(false)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $price = RawMaterial::find($state)?->price ?? null;
                            $set('price_per_unit', $price);
                            $set('total_price', intval($price) * intval($get('quantity')) ?? 0);
                        }),

                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(User::query()->where('role', 'supplier')->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->preload()
                        ->native(false),
                ]),

            Forms\Components\Wizard\Step::make('Specifications')
                ->description('Set quantity and calculate pricing')
                ->icon('heroicon-o-calculator')
                ->schema([
                    TextInput::make('quantity')
                        ->required()
                        ->live()
                        ->numeric()
                        ->minValue(1)
                        ->suffix(fn($get) => RawMaterial::find($get('raw_materials_id'))?->unit_of_measure ?? 'units')
                        ->default(1)
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $price = $get('price_per_unit') ?? 0;
                            $set('total_price', $state * $price);
                        }),

                    TextInput::make('price_per_unit')
                        ->numeric()
                        ->required()
                        ->prefix('UGX')
                        ->reactive()
                        ->readonly()
                        ->disabled(fn($get) => !$get('raw_materials_id'))
                        ->dehydrated(true)
                        ->live()
                        ->extraAttributes(['readonly' => true])
                        ->visible(fn($get) => $get('raw_materials_id'))
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            $quantity = $get('quantity') ?? 0;
                            $set('total_price', $quantity * $state);
                        }),
                ]),

            Forms\Components\Wizard\Step::make('Delivery')
                ->description('Select delivery option and review total')
                ->icon('heroicon-o-truck')
                ->schema([
                    TextInput::make('total_price')
                        ->disabled()
                        ->numeric()
                        ->dehydrated()
                        ->prefix('UGX')
                        ->extraAttributes(['readonly' => true])
                        ->visible(fn($get) => $get('quantity') && $get('price_per_unit'))
                        ->default(0)
                        ->reactive(),

                    Select::make('delivery_option')
                        ->options([
                            'delivery' => 'Delivery',
                            'pickup' => 'Pickup',
                        ])
                        ->default('delivery')
                        ->native(false)
                        ->required(),
                ]),

            Forms\Components\Wizard\Step::make('Review & Notes')
                ->description('Add any final comments or notes')
                ->icon('heroicon-o-clipboard-document')
                ->schema([
                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3)
                        ->placeholder('Any additional notes regarding the order'),
                ]),
        ])->columnSpanFull(),
    ]);
}


    public static function table(Table $table): Table
    {
        return $table
            ->query(

                function () {
                    if (Auth::user()->role == 'supplier') {
                        return RawMaterialsPurchaseOrder::query()->where('supplier_id', Auth::id())->latest();
                    } else {
                        return RawMaterialsPurchaseOrder::query()->where('created_by', Auth::id())->latest();
                    }
                }
            )
            ->columns([
                Tables\Columns\TextColumn::make('rawMaterial.name')
                    ->label('Raw Material')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->sortable()
                    ->searchable()
                    ->visible(fn($record) => Auth::user()?->role == 'manufacturer'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric($precision = 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('rawMaterial.unit_of_measure')
                    ->label('Unit of Measure'),
                Tables\Columns\TextColumn::make('price_per_unit')
                    ->label('Price per Unit (UGX)')
                    ->numeric($precision = 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Price (UGX)')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Order Date')
                    ->dateTime()
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->getStateUsing(function ($record) {
                        if ($record->expected_delivery_date < now() || $record->status == 'cancelled') {
                            return 'Closed';
                        } elseif ($record->status == 'delivered') {
                            return 'Done';
                        }
                        return \Carbon\Carbon::parse($record->expected_delivery_date)->diffForHumans([
                            'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
                            'parts' => 2,
                        ]);
                    })
                    ->label('Expected Delivery Date'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger',
                        'delivered' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('delivery_option'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('ResumeOrder')
                    ->icon('heroicon-o-play')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record, $livewire) {
                        $record->update(['status' => 'pending']);
                        
                        $livewire->dispatch('sweetalert', [
                            'title' => 'Order Resumed Successfully',
                            'icon' => 'success',
                            'iconColor' => 'green'
                        ]);
                    })->visible(fn($record) => $record->status == 'cancelled' && Auth::User()->role == 'manufacturer')
                    ->label('Resume Order'),
                Action::make('markAsDelivered')
                    ->label('Mark As Delivered')
                    ->icon('heroicon-o-truck')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($record, $livewire) {
                        $rawMaterial = RawMaterial::find($record->raw_materials_id);
                        if ($rawMaterial) {
                            $rawMaterial->update([
                                'current_stock' => $rawMaterial->current_stock + $record->quantity
                            ]);
                        }
                        $record->update(['status' => 'delivered']);

                        $livewire->dispatch('sweetalert', [
                            'title' => 'Order marked as delivered - Raw material received and current stock updated.',
                            'icon' => 'success',
                            'iconColor' => 'green'
                        ]);
                    })
                    ->visible(fn($record) => $record->status == 'confirmed' && Auth::User()->role == 'manufacturer'),
                Action::make('CancelOrder')
                    ->label('Cancel Order')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'cancelled']);
                        
                        // Use JavaScript to dispatch Livewire event
                        echo "<script>
                            if (typeof Livewire !== 'undefined') {
                                Livewire.dispatch('sweetalert', {
                                    title: 'Order cancelled',
                                    icon: 'success',
                                    iconColor: 'green'
                                });
                            }
                        </script>";
                    })
                    ->visible(fn($record) => $record->status == 'pending' && Auth::User()->role == 'manufacturer'),
                Action::make('ConfirmOrder')
                    ->label('Confirm Order')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['status' => 'confirmed']);
                        
                        // Use JavaScript to dispatch Livewire event
                        echo "<script>
                            if (typeof Livewire !== 'undefined') {
                                Livewire.dispatch('sweetalert', {
                                    title: 'Order confirmed',
                                    icon: 'success',
                                    iconColor: 'green'
                                });
                            }
                        </script>";
                    })
                    ->visible(fn($record) => $record->status == 'pending' && Auth::User()->role == 'supplier'),
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn($record) => Auth::user()?->role === 'manufacturer'),

            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => Pages\ListRawMaterialsPurchaseOrders::route('/'),
            'create' => Pages\CreateRawMaterialsPurchaseOrder::route('/create'),
            // 'view' => Pages\ViewRawMaterialsPurchaseOrder::route('/{record}'),
            // 'edit' => Pages\EditRawMaterialsPurchaseOrder::route('/{record}/edit'),
        ];
    }
}