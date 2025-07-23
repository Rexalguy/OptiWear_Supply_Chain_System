<?php

namespace App\Filament\Manufacturer\Pages;

use Filament\Tables;
use Filament\Pages\Page;
use App\Models\Workforce;;
use App\Models\ProductionOrder;
use App\Models\ProductionStage;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Tables\Contracts\HasTable;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;

class DeliveryStage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Production Workflow';

   
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Delivery';
    protected static string $view = 'filament.manufacturer.pages.delivery-stage';

    public static function getNavigationBadge(): ?string
{
    return (string) ProductionStage::where('stage', 'delivery')
        ->where('status', 'in_progress')
        ->count();
}

public static function getNavigationBadgeColor(): ?string
{
    $count = ProductionStage::where('stage', 'delivery')
        ->where('status', 'in_progress')
        ->count();

    return $count > 0 ? 'info' : 'gray';
}

public static function getNavigationBadgeTooltip(): ?string
{
    return 'delivery tasks in progress';
}

    public function getTableQuery(): Builder
    {
        return ProductionOrder::with('productionStages.workforce')
            ->whereHas('productionStages', function ($query) {
                $query->where('stage', 'printing')->where('status', 'completed');
            })
            ->whereHas('productionStages', function ($query) {
                $query->where('stage', 'packaging')->where('status', 'completed');
            })
            ->whereHas('productionStages', function ($query) {
                $query->where('stage', 'delivery')
                      ->whereIn('status', ['pending', 'in_progress']);
            });
    }

    public function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('product.name')->label('Product'),
            Tables\Columns\TextColumn::make('quantity'),
            Tables\Columns\TextColumn::make('delivery_status')
                ->label('Delivery Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'gray',
                    'in_progress' => 'warning',
                    'completed' => 'success',
                    default => 'secondary',
                })
                ->getStateUsing(fn ($record) =>
                    optional($record->productionStages->firstWhere('stage', 'delivery'))->status ?? '-'
                ),
            Tables\Columns\TextColumn::make('delivery_worker')
                ->label('Assigned To')
                ->icon('heroicon-m-wrench')
                ->getStateUsing(fn ($record) =>
                    optional($record->productionStages->firstWhere('stage', 'delivery'))->workforce->name ?? 'Unassigned'
                ),
        ];
    }

    public function getTableActions(): array
    {
        return [
            Action::make('assignWorkforce')
                ->label('Assign Delivery Worker')
                ->form([
                    Select::make('workforces_id')
                        ->label('Select Worker')
                        ->options(Workforce::where('job', 'delivery')->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data, ProductionOrder $record) {
                    $stage = $record->productionStages()->where('stage', 'delivery')->first();

                    if (! $stage) {
                        $stage = $record->productionStages()->create([
                            'stage' => 'delivery',
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
                        'title' => 'Delivery started',
                        'icon' => 'success',
                        'iconColor' => 'green',
                    ]);
                })
                ->visible(fn (ProductionOrder $record) =>
                    optional($record->productionStages->firstWhere('stage', 'delivery'))->status === 'pending'
                ),

            Action::make('completeDelivery')
                ->label('Mark as Delivered')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (ProductionOrder $record) {
                    $delivery = $record->productionStages()->where('stage', 'delivery')->first();

                    if ($delivery && $delivery->status === 'in_progress') {
                            $delivery->update([
                                'status' => 'completed',
                                'completed_at' => now(),
                            ]);

                            // ✅ Increase product quantity
                            $record->product->increment('quantity_available', $record->quantity);

                            $record->update(['status' => 'completed']);

                            $this->dispatch('cart-updated', [
                                'title' => 'Delivery completed. Product stock updated.',
                                'icon' => 'success',
                                'iconColor' => 'green',
                            ]);
                        }
                })
                ->visible(fn (ProductionOrder $record) =>
                    optional($record->productionStages->firstWhere('stage', 'delivery'))->status === 'in_progress'
                ),
        ];
    }
}
