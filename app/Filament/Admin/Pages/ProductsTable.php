<?php

namespace App\Filament\Admin\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class ProductsTable extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-beaker';

     protected static ?string $navigationGroup = 'Product Supervison';

    protected static string $view = 'filament.admin.pages.products-table';

    public function table(Table $table): Table
{
    return $table
        ->query(Product::query())
        ->columns([
            TextColumn::make('name')->label('Product Name')
            ->searchable()
            ->sortable(),
            TextColumn::make('ShirtCategory.category')->label('Category')
            ->searchable()
            ->sortable(),
            TextColumn::make('quantity_available')->label('Stock'),
            TextColumn::make('unit_price')->money('UGX')->label('Price'),
        ]);
}

}
