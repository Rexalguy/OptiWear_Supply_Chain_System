<?php

namespace App\Filament\Manufacturer\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ProductionOrder;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
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
                

                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price (UGX)')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity_available')
                    ->numeric()
                    ->alignCenter()
                    ->badge()
                        ->colors([
                            'danger' => fn ($record) => $record->quantity_available < $record->low_stock_threshold,
                            'warning' => fn ($record) => $record->quantity_available <= $record->low_stock_threshold + 100,
                            'success' => fn ($record) => $record->quantity_available > $record->low_stock_threshold + 100,
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
                Tables\Actions\Action::make('autoReorder')
                    ->icon('heroicon-o-plus-circle')
                    ->iconButton()
                    ->color('warning')
                    ->tooltip('Auto reorder products')
                    ->visible(fn (Product $record) => $record->quantity_available <= $record->low_stock_threshold + 100)
                    ->form(function (Product $record): array {
                        $suggestedQuantity = max(
                            ($record->low_stock_threshold * 2) - $record->quantity_available,
                            1
                        );

                        return [
                            Forms\Components\TextInput::make('quantity')
                                ->label('Quantity to Produce')
                                ->required()
                                ->numeric()
                                ->minValue(1)
                                ->default($suggestedQuantity),
                        ];
                    })

                    ->action(function (array $data, Product $record): void {
                        ProductionOrder::create([
                            'product_id' => $record->id,
                            'quantity' => $data['quantity'],
                            'status' => 'pending',
                            'created_by' => Auth::id(),
                        ]);

                        Notification::make()
                            ->title("Production order created for {$record->name}")
                            ->success()
                            ->send();
                    }),

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
