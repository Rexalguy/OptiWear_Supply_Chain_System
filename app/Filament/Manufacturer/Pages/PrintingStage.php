<?php

namespace App\Filament\Manufacturer\Pages;

use Filament\Pages\Page;
use App\Models\ProductionStage;
use App\Models\Workforce;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class PrintingStage extends Page implements HasTable
{
use InteractsWithTable;

    protected static ?string $title = 'Printing Stage';
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationGroup = 'Production Workflow';
    protected static string $view = 'filament.manufacturer.pages.printing-stage';

    public function getTableQuery(): Builder
    {
        return ProductionStage::with(['productionOrder.product', 'workforce'])
            ->where('stage', 'printing')
            ->where('status', '!=', 'completed');;
    }

    public function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('productionOrder.product.name')->label('Product'),
            Tables\Columns\TextColumn::make('productionOrder.quantity')->label('Quantity'),
            Tables\Columns\TextColumn::make('status')->badge(),
            Tables\Columns\TextColumn::make('workforce.name')->label('Assigned To')->placeholder('Unassigned'),
        ];
    }

    public function getTableActions(): array
    {
        return [
            Action::make('assign')
                ->label('Assign Worker')
                ->form([
                    Select::make('workforce_id')
                        ->label('Printing Workforce')
                        ->options(Workforce::where('job', 'printing')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data, ProductionStage $record) {
                    $record->update([
                        'workforce_id' => $data['workforce_id'],
                        'status' => 'in_progress',
                        'started_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Worker assigned and printing started.')
                        ->success()
                        ->send();
                })
                ->visible(fn ($record) => $record->status === 'pending'),

            Action::make('complete')
                ->label('Mark as Completed')
                ->requiresConfirmation()
                ->color('success')
                ->action(function (ProductionStage $record) {
                    $record->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);

                    // Move to Packaging Stage
                    $record->productionOrder->productionStages()->create([
                        'stage' => 'packaging',
                        'status' => 'pending',
                    ]);

                    Notification::make()
                        ->title('Printing completed. Moved to packaging.')
                        ->success()
                        ->send();
                })
                ->visible(fn ($record) => $record->status === 'in_progress'),
        ];
    }
}
