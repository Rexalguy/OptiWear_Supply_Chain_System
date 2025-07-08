<?php

namespace App\Filament\Manufacturer\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use App\Models\RawMaterialsPurchaseOrder;
use Illuminate\Database\Eloquent\Builder;
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
                    ->prefix('$')
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
        
Josemiles@Joseph MINGW64 ~/Desktop/SCM (newMain)
$ git status
On branch newMain
Your branch is up to date with 'otherRepo/newMain'.

nothing to commit, working tree clean

Josemiles@Joseph MINGW64 ~/Desktop/SCM (newMain)
$ php artisan cache:clear

   INFO  Application cache cleared successfully.


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