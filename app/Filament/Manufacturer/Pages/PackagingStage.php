<?php

namespace App\Filament\Manufacturer\Pages;

use Filament\Tables;
use Filament\Pages\Page;
use App\Models\Workforce;
use App\Models\ProductionOrder;
use Filament\Tables\Actions\Action;
use App\Models\ProductionStage;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;

class PackagingStage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Production Workflow';
    protected static ?string $title = 'Packaging';
    protected static string $view = 'filament.manufacturer.pages.packaging-stage';

    public function getTableQuery(): Builder
    {
        return ProductionOrder::with('productionStages.workforce') // eager-load to prevent N+1
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
                ->getStateUsing(function ($record) {
                    return optional($record->productionStages->firstWhere('stage', 'packaging'))->status ?? '-';
                }),

            Tables\Columns\TextColumn::make('packaging_worker')
                ->label('Assigned To')
                ->getStateUsing(function ($record) {
                    return optional($record->productionStages->firstWhere('stage', 'packaging'))->workforce->name ?? 'Unassigned';
                }),
        ];
    }

    public function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('assignWorkforce')
                ->label('Assign Packaging Worker')
                ->form([
                    Select::make('workforce_id')
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
                            'workforce_id' => $data['workforce_id'],
                            'status' => 'in_progress',
                            'started_at' => now(),
                        ]);
                    }

                    Notification::make()
                        ->title('Packaging started')
                        ->success()
                        ->send();
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
                        $packaging->update([
                            'status' => 'completed',
                            'completed_at' => now(),
                        ]);

                        $record->productionStages()->create([
                            'stage' => 'delivery',
                            'status' => 'pending',
                        ]);

                        Notification::make()
                            ->title('Packaging completed. Delivery stage created.')
                            ->success()
                            ->send();
                    }
                })
                ->visible(fn (ProductionOrder $record) => 
                    optional($record->productionStages->firstWhere('stage', 'packaging'))->status === 'in_progress'
                ),
        ];
    }
}
