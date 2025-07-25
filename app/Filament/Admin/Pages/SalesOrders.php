<?php

namespace App\Filament\Admin\Pages;

use App\Models\Order;

use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Concerns\InteractsWithTable;

class SalesOrders extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationGroup = 'Sales';

    protected static string $view = 'filament.admin.pages.sales-orders';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->latest())
            ->columns([
                TextColumn::make('id')->label('Order #')->sortable(),
                TextColumn::make('creator.name')->label('Customer'),
                TextColumn::make('status')->badge()->color(fn(string $state) => match ($state) {
                    'pending' => 'warning',
                    'processing' => 'info',
                    'completed' => 'success',
                    'cancelled' => 'danger',
                    default => 'gray',
                }),
                TextColumn::make('total')->label('Amount')->money('UGX'),
                TextColumn::make('created_at')->label('Date')->dateTime(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Order Status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                    ])
                    ->placeholder('All'),
            ]);
    }
}
