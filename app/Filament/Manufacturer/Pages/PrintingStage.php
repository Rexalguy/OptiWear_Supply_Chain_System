<?php

namespace App\Filament\Manufacturer\Pages;

use Filament\Pages\Page;
use App\Models\ProductionStage;
use App\Models\Workforce;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\Action;
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
            ->where('status', '!=', 'completed');
    }

    public function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('productionOrder.product.name')->label('Product'),
            Tables\Columns\TextColumn::make('productionOrder.quantity')->label('Quantity'),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'gray',
                    'in_progress' => 'warning',
                    'completed' => 'success',
                    default => 'secondary',
                }),
            Tables\Columns\TextColumn::make('workforce.name')
                ->label('Assigned To')
                ->placeholder('Unassigned')
                ->icon('heroicon-m-wrench'),
        ];
    }

    public function getTableActions(): array
    {
        return [
            Action::make('complete')
                ->label('Mark as Completed')
                ->requiresConfirmation()
                ->color('success')
                ->action(function (ProductionStage $record) {
                    // Complete current stage
                    $record->update([
                        'status' => 'completed',
                        'completed_at' => now(),
                    ]);

                    // Free the current worker
                    if ($record->workforce) {
                        $record->workforce->update(['is_available' => true]);
                    }

                    // Create new packaging stage
                    $packagingStage = $record->productionOrder->productionStages()->create([
                        'stage' => 'packaging',
                        'status' => 'pending',
                    ]);

                    // Try to auto-assign a packaging worker
                    $packagingWorker = Workforce::where('job', 'packaging')
                        ->where('is_available', true)
                        ->first();

                    if ($packagingWorker) {
                        $packagingStage->update([
                            'workforces_id' => $packagingWorker->id,
                            'status' => 'in_progress',
                            'started_at' => now(),
                        ]);

                        $packagingWorker->update(['is_available' => false]);

                        Notification::make()
                            ->title("Packaging started. Assigned to: {$packagingWorker->name}")
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Printing completed.')
                            ->body('Moved to packaging. No available packaging worker.')
                            ->warning()
                            ->send();
                    }
                })
                ->visible(fn ($record) => $record->status === 'in_progress'),
        ];
    }
}
