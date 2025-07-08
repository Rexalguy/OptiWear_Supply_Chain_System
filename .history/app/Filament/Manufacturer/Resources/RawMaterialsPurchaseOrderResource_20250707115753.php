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
    protected static ?string $navigationGroup ='Raw Materials';
    public static function canViewAny(): bool {
    return Auth::user()?->role == 'manufacturer' || Auth::user()?->role == 'supplier';
}
    public static function shouldRegisterNavigation(): bool {
        return Auth::user()?->role === 'manufacturer' || Auth::user()?->role === 'supplier';
    }
    public static function canCreate(): bool {
        return auth()->user()?->role === 'manufacturer';
    }
    public static function getEloquentQuery(): Builder
    {
    return parent::getEloquentQuery()
        ->orderByDesc('created_at');
    }

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Raw materials Purchase Order Details')
                ->description('Fill in the details for the raw materials purchase order.')
                ->columns(2)
                ->schema([
                    Select::make('raw_material_id')
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
            Section::make('Order Specifications')
            ->description('Specify the order details such as quantity. Price already set based on raw material selected.')
            ->columns(2)
            ->schema([
                TextInput::make('quantity')
                ->required()
                ->live()
                ->numeric()
                ->minValue(1)
                ->suffix(fn ($get) => RawMaterial::find($get('raw_material_id'))?->unit_of_measure ?? 'units')
                ->default(1)
                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                    $price = $get('price_per_unit') ?? 0;
                    $set('total_price', $state * $price);
                }),
                TextInput::make('price_per_unit')
                ->numeric()
                ->required()
                ->prefix('$')
                ->reactive()
                ->readonly()
                ->disabled(fn ($get) => ! $get('raw_material_id'))
                ->dehydrated(true)
                ->live()
                ->extraAttributes(['readonly' => true])
                ->visible(fn ($get) => $get('raw_material_id'))
                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                    $quantity = $get('quantity') ?? 0;
                    $set('total_price', $quantity * $state);
                })
            ]),
                Section::make('Total Purchase Order Price and Delivery Option')
                ->description('Total price is calculated based on quantity and price per unit.')
                ->columns(2)
                ->schema([
                    TextInput::make('total_price')
                    ->disabled()
                    ->numeric()
                    ->dehydrated()
                    ->prefix('UGX')
                    ->extraAttributes(['readonly' => true])
                    ->visible(fn ($get) => $get('quantity') && $get('price_per_unit'))
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
                Section::make('Additional Information')
                ->description('Provide additional information about the order.')
                ->columnSpanFull()
                ->schema([
                    Textarea::make('notes')
                ->label('Notes')
                ->rows(3)
                ->placeholder('Any additional notes regarding the order'),
                ])

      ]);
}
    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('rawMaterial.name')
                ->label('Raw Material')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('supplier.name')
                ->sortable()
                ->searchable()
                ->visible(fn ($record) => Auth::user()?->role == 'manufacturer'),
            Tables\Columns\TextColumn::make('quantity')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('price_per_unit')
                ->numeric(2) // Set to 2 decimal places
                ->money('UGX')
                ->sortable(),
            Tables\Columns\TextColumn::make('total_price')
                ->numeric(2) // Set to 2 decimal places
                ->money('UGX')
                ->sortable(),
            Tables\Columns\TextColumn::make('order_date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('expected_delivery_date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->searchable()
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'confirmed' => 'success',
                    'cancelled' => 'danger',
                    'delivered' => 'success',
                    default => 'gray',
                }),
            Tables\Columns\TextColumn::make('delivery_option'),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            
        ])
        ->filters([
            //
        ])
        ->actions([
            Action::make('ResumeOrder')
            ->icon('heroicon-o-play')
            ->color('success')
            ->action(function ($record) {
                $record->update(['status' => 'pending']);
                Notification::make()
                    ->title('Order Resumed Successfully')
                    ->success()
                    ->send();
            })->visible(fn ($record)=> $record->status == 'cancelled' && Auth::User()->role == 'manufacturer')
            ->label('Resume Order'),
            Action::make('markAsDelivered')
            ->label('Mark As Delivered')
            ->icon('heroicon-o-truck')
            ->color('success')
            ->action(function ($record) {
                $rawMaterial = RawMaterial::find($record->raw_material_id);
                if ($rawMaterial) {
                    $rawMaterial->update([
                        'current_stock' => $rawMaterial->current_stock + $record->quantity
                    ]);
                }
                $record->update(['status' => 'delivered']);
                
                Notification::make()
                ->title('Order marked as delivered')
                ->body('Raw material received and current stock updated.')
                ->success()
                ->send();
            })
            ->visible(fn ($record) => $record->status == 'confirmed' && Auth::User()->role=='manufacturer'),
            Action::make('CancelOrder')
            ->label('Cancel Order')
            ->icon('heroicon-o-x-mark')
            ->color('danger')
            ->action(function ($record) {
                $record->update(['status' => 'cancelled']);
                Notification::make()
                ->title('Order cancelled')
                ->success()
                ->send();
            })
            ->visible(fn ($record) => $record->status == 'pending' && Auth::User()->role == 'manufacturer'),
            Action::make('ConfirmOrder')
            ->label('Confirm Order')
            ->icon('heroicon-o-check')
            ->color('success')
            ->action(function ($record) {
                $record->update(['status' => 'confirmed']);
                Notification::make()
                ->title('Order confirmed')
                ->success()
                ->send();
            })
            ->visible(fn ($record) => $record->status == 'pending' && Auth::User()->role == 'supplier'),
            ViewAction::make(),
            EditAction::make()
            ->visible(fn ($record) => Auth::user()?->role === 'manufacturer'),
            
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
            'view' => Pages\ViewRawMaterialsPurchaseOrder::route('/{record}'),
            'edit' => Pages\EditRawMaterialsPurchaseOrder::route('/{record}/edit'),
        ];
    }
}