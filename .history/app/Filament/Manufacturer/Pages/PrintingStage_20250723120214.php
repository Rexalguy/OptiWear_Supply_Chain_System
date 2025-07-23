<?php

namespace App\Filament\Manufacturer\Pages;

use Filament\Tables;
use Filament\Pages\Page;
use App\Models\Workforce;
use App\Models\ProductionStage;
use Filament\Tables\Actions\Action;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Manufacturer\Widgets\StageStatsWidget;

class PrintingStage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Printing Stage';
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationGroup = 'Production Workflow';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.manufacturer.pages.printing-stage';

    public static function getNavigationBadge(): ?string
    {
        return (string) ProductionStage::where('stage', 'printing')
            ->where('status', 'in_progress')
            ->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = ProductionStage::where('stage', 'printing')
            ->where('status', 'in_progress')
            ->count();

        return $count > 0 ? 'info' : 'gray';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Printing tasks in progress';
    }

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
                ->color(fn(string $state): string => match ($state) {
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

                        $this->dispatch('cart-updated', [
                            'title' => "Packaging started. Assigned to: {$packagingWorker->name}",
                            'icon' => 'success',
                            'iconColor' => 'green',
                        ]);
                    } else {
                        $this->dispatch('cart-updated', [
                            'title' => 'Printing completed.',
                            'text' => 'Moved to packaging. No available packaging worker.',
                            'icon' => 'warning',
                            'iconColor' => 'orange',
                        ]);
                    }
                })
                ->visible(fn($record) => $record->status === 'in_progress'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StageStatsWidget::make(['stage' => 'printing']),
        ];
    }
}
