<?php

namespace App\Filament\Manufacturer\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BillOfMaterial;
use App\Models\ProductionOrder;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Manufacturer\Resources\ProductionOrderResource\Pages;
use App\Filament\Manufacturer\Resources\ProductionOrderResource\RelationManagers;

class ProductionOrderResource extends Resource
{
    protected static ?string $model = ProductionOrder::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Select::make('product_id')
                ->label('Product')
                ->options(Product::pluck('name', 'id'))
                ->searchable()
                ->required(),

            TextInput::make('quantity')
                ->label('Quantity to Produce')
                ->numeric()
                ->minValue(1)
                ->default(1)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('product.name')->label('Product'),
            Tables\Columns\TextColumn::make('quantity'),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),

            ])
            ->filters([
                //
            ])
            ->actions([
            Tables\Actions\Action::make('build')
            ->label('Build')
            ->icon('heroicon-o-cog-6-tooth')
            ->color('success')
            ->requiresConfirmation()
            ->action(function (ProductionOrder $record) {
                $materials = BillOfMaterial::where('product_id', $record->product_id)->get();

                foreach ($materials as $material) {
                    $raw = $material->rawMaterial;
                    $required = $material->quantity_required * $record->quantity;

                    if ($raw->quantity_in_stock < $required) {
                        Notification::make()
                            ->title("Insufficient stock for {$raw->name}")
                            ->danger()
                            ->send();
                        return;
                    }
                }

                foreach ($materials as $material) {
                    $raw = $material->rawMaterial;
                    $required = $material->quantity_required * $record->quantity;
                    $raw->decrement('quantity_in_stock', $required);
                }

                $record->product->increment('quantity_in_stock', $record->quantity);
                $record->update(['status' => 'completed']);

                Notification::make()
                    ->title('Production complete!')
                    ->success()
                    ->send();
            })
            ->visible(fn (ProductionOrder $record) => $record->status === 'pending'),


            // 🆕 BOM Action
           Tables\Actions\Action::make('bom')
    ->label('BOM')
    ->icon('heroicon-o-information-circle')
    ->modalHeading('Required Raw Materials')
    ->infolist(function (ProductionOrder $record): array {
        $materials = BillOfMaterial::with('rawMaterial')
            ->where('product_id', $record->product_id)
            ->get();

        if ($materials->isEmpty()) {
            return [
                Section::make('BOM Details')->schema([
                    TextEntry::make('empty')
                        ->default('No Bill of Materials defined for this product.')
                        ->columnSpanFull()
                        ->color('danger'),
                ]),
            ];
        }

        return [
            Section::make('Required Materials')
                ->description("Based on quantity: {$record->quantity}")
                ->schema(
                    $materials->map(function ($material) use ($record) {
                        return Grid::make(3)->schema([
                            TextEntry::make('material')
                                ->label('Material')
                                ->default($material->rawMaterial->name),

                            TextEntry::make('required_quantity')
                                ->label('Quantity')
                                ->default($material->quantity_required * $record->quantity),

                            TextEntry::make('unit')
                                ->label('Unit')
                                ->default($material->rawMaterial->unit),
                        ]);
                    })->toArray()
                ),
        ];
    })
    ->modalSubmitAction(false)
    ->modalCancelActionLabel('Close')
    ,
            
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
            'index' => Pages\ListProductionOrders::route('/'),
            // 'create' => Pages\CreateProductionOrder::route('/create'),
            // 'view' => Pages\ViewProductionOrder::route('/{record}'),
            // 'edit' => Pages\EditProductionOrder::route('/{record}/edit'),
        ];
    }
}
