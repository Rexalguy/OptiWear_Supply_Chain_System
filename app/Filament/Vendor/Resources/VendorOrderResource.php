<?php

namespace App\Filament\Vendor\Resources;

use App\Filament\Vendor\Resources\VendorOrderResource\Pages;
use App\Filament\Vendor\Resources\VendorOrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendorOrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getEloquentQuery(): Builder
{
    return parent::getEloquentQuery()
        ->when(\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->isVendor(), fn ($q) => $q->where('vendor_id', \Illuminate\Support\Facades\Auth::id()));
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
            Forms\Components\Select::make('delivery_method')
                ->options(['pickup' => 'Pickup', 'delivery' => 'Delivery'])
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('items.product.name')->label('Product'),
                Tables\Columns\TextColumn::make('items.quantity')->label('Qty'),
                Tables\Columns\TextColumn::make('items.total_price')->label('Total Price'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                       'pending' => 'warning',
                       'confirmed' => 'success',
                       'declined' => 'danger',
                       'cancelled' => 'gray',
                        default => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('expected_fulfillment_date')->label('Fulfillment'),
                    ])
        
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('cancel')
                    ->label('Cancel Order')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'cancelled'])),
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
            'index' => Pages\ListVendorOrders::route('/'),
            'create' => Pages\CreateVendorOrder::route('/create'),
            'edit' => Pages\EditVendorOrder::route('/{record}/edit'),
        ];
    }
}
