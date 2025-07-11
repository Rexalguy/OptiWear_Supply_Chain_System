<?php

namespace App\Filament\Manufacturer\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Manufacturer\Resources\ProductResource\Pages;
use App\Filament\Manufacturer\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationGroup = 'Product';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    public static function canCreate(): bool
    {
        return false; // Completely removes create functionality
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('image')
                    ->label('Image URL')
                    ->url()
                    ->placeholder('https://example.com/image.webp')
                    ->required(),
                    
                    

                Forms\Components\TextInput::make('sku')
                    ->label('SKU')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->label('Price (UGX)')
                    ->required()
                    ->numeric(),
                    
                Forms\Components\TextInput::make('quantity_available')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('low_stock_threshold')
                    ->numeric()
                    ->default(10),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\ImageColumn::make('image')
                    ->label('Product Image')
                    ->height(80)
                    ->width(80)
                    ->circular(), // important!
                

                Tables\Columns\TextInputColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity_available')
                    ->numeric()
                    ->badge()
                        ->colors([
                            'danger' => fn ($record) => $record->quantity_available < $record->low_stock_threshold,
                            'warning' => fn ($record) => $record->quantity_available == $record->low_stock_threshold,
                            'success' => fn ($record) => $record->quantity_available > $record->low_stock_threshold,
                        ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Tables\Columns\TextColumn::make('low_stock_threshold')
                //     ->numeric()
                //     ->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProducts::route('/'),
        ];
    }
}
