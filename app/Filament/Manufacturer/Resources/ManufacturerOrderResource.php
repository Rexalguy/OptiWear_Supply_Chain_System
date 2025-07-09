<?php

namespace App\Filament\Manufacturer\Resources;

use App\Filament\Manufacturer\Resources\ManufacturerOrderResource\Pages;
use App\Filament\Manufacturer\Resources\ManufacturerOrderResource\RelationManagers;
use App\Models\ManufacturerOrder;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManufacturerOrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('manufacturer_id', \Illuminate\Support\Facades\Auth::user()->id);
    }
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('vendor.name')->label('Vendor'),
                Tables\Columns\TextColumn::make('items_summary')
            ->label('Products')
            ->getStateUsing(fn ($record) => $record->items->pluck('product.name')->implode(', ')),
                Tables\Columns\TextColumn::make('quantity')
            ->label('Qty')
            ->getStateUsing(fn ($record) => $record->items->sum('quantity')),
                Tables\Columns\TextColumn::make('price')
                ->label('Unit Price')
                ->money('UGX', true),
                Tables\Columns\TextColumn::make('total_amount')
             ->label('Total Price')
             ->getStateUsing(fn ($record) => $record->quantity * $record->unit_price)
             ->money('UGX', true),

                Tables\Columns\TextColumn::make('created_at')->label('Ordered At')->dateTime('d-M-Y H:i'),

                Tables\Columns\TextColumn::make('status')
             ->label('Status')
             ->color(fn ($state) => match($state) {
                       'pending' => 'warning',
                       'delivered' => 'success',
                       'cancelled' => 'danger',
                       'declined' => 'danger',
    })
           ->extraAttributes(function ($record) {
        if ($record->status === 'declined') {
            return [
                'title' => 'Reason: ' . $record->decline_reason, // shows on hover
            ];
        }

        return [];
    })
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('confirm')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'confirmed',
                            'expected_fulfillment_date' => now()->addDays(5)
                        ]);
                        foreach ($record->items as $item) {
                            $item->product->decrement('quantity', $item->quantity);
                        }
                         }),
                Tables\Actions\Action::make('decline')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('decline_reason')->required(),
                    ])
                    ->action(function ($record, $data) {
                        $record->update(['status' => 'declined', 'decline_reason' => $data['decline_reason']]);
                    }),
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
            'index' => Pages\ListManufacturerOrders::route('/'),
            'create' => Pages\CreateManufacturerOrder::route('/create'),
            'edit' => Pages\EditManufacturerOrder::route('/{record}/edit'),
        ];
    }
}
