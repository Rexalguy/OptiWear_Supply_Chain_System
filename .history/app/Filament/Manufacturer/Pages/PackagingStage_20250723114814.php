<?php

namespace App\Filament\Manufacturer\Pages;

use Filament\Tables;
use Filament\Pages\Page;
use App\Models\Workforce;
use App\Models\ProductionOrder;
use App\Models\ProductionStage;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;
use App\Filament\Manufacturer\Widgets\StageStatsWidget;

class PackagingStage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Production Workflow';

    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'Packaging';
    protected static string $view = 'filament.manufacturer.pages.packaging-stage';

    public static function getNavigationBadge(): ?string
{
    return (string) ProductionStage::where('stage', 'packaging')
        ->where('status', 'in_progress')
        ->count();
}

public static function getNavigationBadgeColor(): ?string
{
    $count = ProductionStage::where('stage', 'packaging')
        ->where('status', 'in_progress')
        ->count();

    return $count > 0 ? 'info' : 'gray';
}

public static function getNavigationBadgeTooltip(): ?string
{
    return 'packaging tasks in progress';
}

    public function getTableQuery(): Builder
    {
        return ProductionOrder::with('productionStages.workforce')
            ->whereHas('productionStages', function ($query) {
                $query->where('stage', 'printing')->where('status', 'completed');
            })
            ->whereHas('productionStages', function ($query) {
                $query->where('stage', 'packaging')->whereIn('status', ['pending','in_progress']);
            });
    }

    public function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('product.name')->label('Product'),
            Tables\Columns\TextColumn::make('quantity'),
            Tables\Columns\TextColumn::make('packaging_status')
                ->label('Packaging Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'gray',
                    'in_progress' => 'warning',
                    'completed' => 'success',
                    default => 'secondary',
                })
                ->getStateUsing(fn ($record) => optional($record->productionStages->firstWhere('stage', 'packaging'))->status ?? '-'),
            Tables\Columns\TextColumn::make('packaging_worker')
                ->label('Assigned To')
                ->icon('heroicon-m-wrench')
                ->getStateUsing(fn ($record) => optional($record->productionStages->firstWhere('stage', 'packaging'))->workforce->name ?? 'Unassigned'),
        ];
    }

    public function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('assignWorkforce')
                ->label('Assign Packaging Worker')
                ->form([
                    Select::make('workforces_id')
                        ->label('Select Worker')
                        ->options(Workforce::where('job', 'packaging')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data, ProductionOrder $record) {
                    $stage = $record->productionStages()->where('stage', 'packaging')->first();

                    if (! $stage) {
                        $stage = $record->productionStages()->create([
                            'stage' => 'packaging',
                            'status' => 'pending',
                        ]);
                    }

                    if ($stage->status === 'pending') {
                        $stage->update([
                            'workforces_id' => $data['workforces_id'],
                            'status' => 'in_progress',
                            'started_at' => now(),
                        ]);
                    }

                    $this->dispatch('cart-updated', [
                        'title' => 'Packaging started',
                        'icon' => 'success',
                        'iconColor' => 'green',
                    ]);
                })
                ->visible(fn (ProductionOrder $record) =>
                    optional($record->productionStages->firstWhere('stage', 'packaging'))->status === 'pending'
                ),

            Tables\Actions\Action::make('completePackaging')
                ->label('Mark as Completed')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (ProductionOrder $record) {
                    $packaging = $record->productionStages()->where('stage', 'packaging')->first();

                    if ($packaging && $packaging->status === 'in_progress') {
                        // Complete packaging stage
                        $packaging->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);

                        // Free packaging worker
                        if ($packaging->workforce) {
                            $packaging->workforce->update(['is_available' => true]);
                        }

                        // Create delivery stage with 'pending' status
                        $deliveryStage = $record->productionStages()->create([
                            'stage' => 'delivery',
                            'status' => 'pending',
                        ]);

                        // Try to auto-assign available delivery worker
                        $deliveryWorker = Workforce::where('job', 'delivery')
                            ->where('is_available', true)
                            ->first();

                        if ($deliveryWorker) {
                            // Assign and mark delivery stage in progress
                            $deliveryStage->update([
                                'workforces_id' => $deliveryWorker->id,
                                'status' => 'in_progress',
                                'started_at' => now(),
                            ]);

                            // Mark delivery worker as unavailable
                            $deliveryWorker->update(['is_available' => false]);

                            Notification::make()
                                ->title("Packaging completed and delivery started. Assigned worker: {$deliveryWorker->name}")
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Packaging completed. No available delivery worker to assign.')
                                ->warning()
                                ->send();
                        }
                    }
                })
                ->visible(fn (ProductionOrder $record) =>
                    optional($record->productionStages->firstWhere('stage', 'packaging'))->status === 'in_progress'
                ),
        ];
    }

        protected function getHeaderWidgets(): array
{
    return [
        StageStatsWidget::make(['stage' => 'packaging']),
    ];
}

}