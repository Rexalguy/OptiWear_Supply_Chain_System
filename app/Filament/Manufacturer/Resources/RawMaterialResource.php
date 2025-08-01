<?php

namespace App\Filament\Manufacturer\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\RawMaterial;
use Filament\Resources\Resource;
use App\Models\RawMaterialCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Manufacturer\Resources\RawMaterialResource\Pages;
use App\Filament\Manufacturer\Resources\RawMaterialResource\RelationManagers;

class RawMaterialResource extends Resource
{
    protected static ?string $model = RawMaterial::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Raw Materials';

    public static function canCreate(): bool
    {
            return false;
    }
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        return $user && $user->role === 'manufacturer';
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('updated_at');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('price')
                    ->label('Price (UGX)')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->native(false)
                    ->options(User::query()->where('role', 'supplier')->pluck('name', 'id'))
                    ->default(null)
                    ->searchable(),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->native(false)
                    ->options(RawMaterialCategory::pluck('name', 'id'))
                    ->required(),
                Forms\Components\TextInput::make('current_stock')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('reorder_level')
                    ->required()
                    ->numeric()
                    ->default(50),
                Forms\Components\Select::make('unit_of_measure')
                    ->options([
                        'kg' => 'Kilogram',
                        'g' => 'Gram',
                        'liter' => 'Liter',
                        'meter' => 'Meter',
                        'piece' => 'Piece',
                    ])->default('kg')
                    ->label('Unit of Measure')
                    ->native(false)
                    ->searchable()
                    ->required(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->numeric($precision = 2)
                    ->label('Unit Price (UGX)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->default('Unknown Supplier')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->default('Uncategorized'),
                Tables\Columns\TextColumn::make('current_stock')
                    ->numeric($precision = 2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_of_measure'),
                Tables\Columns\TextColumn::make('Comment')
                    ->getStateUsing(function ($record) {
                        $current = (int) $record->current_stock;
                        $reorder = (int) $record->reorder_level;

                        if ($current < $reorder) {
                            return 'Out Of Stock';
                        } elseif ($current >= $reorder && $current < ($reorder * 2)) {
                            return 'Running Out';
                        } else {
                            return 'Still In Stock';
                        }
                    })->badge()
                    ->color(fn($state) => match ($state) {
                        'Out Of Stock' => 'danger',
                        'Running Out' => 'warning',
                        'Still In Stock' => 'success'
                    })
                    ->label('Comment'),

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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListRawMaterials::route('/'),
            'create' => Pages\CreateRawMaterial::route('/create'),
            // 'view' => Pages\ViewRawMaterial::route('/{record}'),
            'edit' => Pages\EditRawMaterial::route('/{record}/edit'),
        ];
    }
}
