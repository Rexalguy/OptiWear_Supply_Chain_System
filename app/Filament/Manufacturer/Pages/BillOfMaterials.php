<?php

namespace App\Filament\Manufacturer\Pages;

use App\Models\BillOfMaterial;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class BillOfMaterials extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = BillOfMaterial::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.manufacturer.pages.bill-of-material';
    
    public function table(Table $table): Table
    {
        // Define your table columns and configuration here
        return $table
            ->query(BillOfMaterial::query())
            ->columns([
                    TextColumn::make('product.name')->label('Product'),
                    TextColumn::make('rawMaterial.name')->label('Raw Material'),
                    TextColumn::make('quantity_required'),

                ])
                ->actions([
                   //
                ])
                ->bulkActions([
                    BulkActionGroup::make([
                        DeleteBulkAction::make(),
                    ]),
                ]); 
                         
    }
    
}
