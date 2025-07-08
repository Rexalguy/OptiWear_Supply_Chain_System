<?php

namespace App\Filament\Manufacturer\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\BillOfMaterial;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Concerns\InteractsWithTable;

class BillOfMaterials extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = BillOfMaterial::class;

    protected static ?string $navigationGroup = 'Product';
    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static string $view = 'filament.manufacturer.pages.bill-of-material';
    
    public function table(Table $table): Table
    {
        // Define your table columns and configuration here
        return $table
            ->query(BillOfMaterial::query())
            ->columns([
                    TextColumn::make('product.name')->label('Product')
                    ->sortable()
                    ->searchable(),
                    TextColumn::make('rawMaterial.name')->label('Raw Material'),
                    TextColumn::make('quantity_required'),
                    TextColumn::make('rawMaterial.unit_of_measure'),
                ])
                ->actions([
                   //
                ])
                ->filters([
                    SelectFilter::make('product_id')
                        ->label('Product')
                        ->options(Product::pluck('name', 'id')->toArray())
                        ->searchable(),
                ])
                
                ->bulkActions([
                    BulkActionGroup::make([
                        DeleteBulkAction::make(),
                    ]),
                ]); 
                         
    }
    
}
