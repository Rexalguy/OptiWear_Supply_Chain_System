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
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Manufacturer\Resources\ProductionOrderResource\Pages;
use App\Filament\Manufacturer\Resources\ProductionOrderResource\Widgets\ProductionStats;

class ProductionOrderResource extends Resource
{
    protected static ?string $model = ProductionOrder::class;
    protected static ?string $navigationGroup = 'Product';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static function getNavigationBadge(): ?string
    {
        return (string) ProductionOrder::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $count = ProductionOrder::where('status', 'pending')->count();

        return $count > 0 ? 'warning' : 'gray'; // yellow if pending, gray if none
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pending production orders';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
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
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Product')->searchable()->sortable()->grow(false),
                Tables\Columns\TextColumn::make('quantity')->label('Quantity')->numeric(),
                Tables\Columns\TextColumn::make('status')->label('Status')->badge()->color(fn($state) => match ($state) {
                    'pending' => 'gray',
                    'in_progress' => 'warning',
                    'completed' => 'success',
                    default => 'gray',
                }),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->since()->sortable()->dateTimeTooltip(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')->label('Status')->options([
                    'pending' => 'Pending',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                ])->native(false),
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Product')
                    ->multiple()
                    ->options(Product::pluck('name', 'id')->toArray())
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('stitch')
                    ->label('Stitch')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (ProductionOrder $record, $livewire) {
                        $materials = BillOfMaterial::where('product_id', $record->product_id)->get();

                        foreach ($materials as $material) {
                            $raw = $material->rawMaterial;
                            $required = $material->quantity_required * $record->quantity;

                            if ($raw->current_stock < $required) {
                                $livewire->dispatch('cart-updated', [
                                    'title' => "Insufficient stock for {$raw->name}",
                                    'icon' => 'error',
                                    'iconColor' => 'red',
                                ]);
                                return;
                            }
                        }

                        foreach ($materials as $material) {
                            $raw = $material->rawMaterial;
                            $required = $material->quantity_required * $record->quantity;
                            $raw->decrement('current_stock', $required);
                        }

                        // Assign printing worker
                        $worker = \App\Models\Workforce::where('job', 'printing')
                            ->where('is_available', true)
                            ->first();

                        if (! $worker) {
                            $livewire->dispatch('cart-updated', [
                                'title' => 'No available printing worker.',
                                'icon' => 'error',
                                'iconColor' => 'red',
                            ]);
                            return;
                        }

                        // Create printing stage
                        $record->productionStages()->create([
                            'stage' => 'printing',
                            'status' => 'in_progress',
                            'workforces_id' => $worker->id,
                            'started_at' => now(),
                        ]);

                        // Mark worker unavailable
                        $worker->update(['is_available' => false]);

                        // Update production order
                        $record->update([
                            'status' => 'in_progress',
                            'created_by' => Auth::id(),
                        ]);

                        $this->dispatch('cart-updated', [
                            'title' => "Stitching started. Assigned worker: {$worker->name}",
                            'icon' => 'success',
                            'iconColor' => 'green',
                        ]);
                    })
                    ->visible(fn(ProductionOrder $record) => $record->status === 'pending'),

                ActionGroup::make([
                    Tables\Actions\Action::make('bom')
                        ->label('View BOM')
                        ->slideOver()
                        ->icon('heroicon-o-information-circle')
                        ->modalHeading('Required Raw Materials')
                        ->infolist(function (ProductionOrder $record): array {
                            $materials = BillOfMaterial::with('rawMaterial')
                                ->where('product_id', $record->product_id)
                                ->get();

                            if ($materials->isEmpty()) {
                                return [
                                    Section::make('No BOM Found')->schema([
                                        TextEntry::make('note')->default('No Bill of Materials defined for this product.')->color('danger'),
                                    ]),
                                ];
                            }

                            return [
                                Section::make('Required Materials')
                                    ->description("Based on quantity: {$record->quantity}")
                                    ->schema(
                                        $materials->map(function ($material) use ($record) {
                                            return Grid::make(3)->schema([
                                                TextEntry::make('material')->label('Material')->default($material->rawMaterial->name),
                                                TextEntry::make('quantity')->label('Quantity')->default($material->quantity_required * $record->quantity),
                                                TextEntry::make('unit')->label('Unit')->default($material->rawMaterial->unit_of_measure),
                                            ]);
                                        })->toArray()
                                    ),
                            ];
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),

                    Tables\Actions\Action::make('viewLog')
                        ->label('Stage Log')
                        ->slideOver()
                        ->icon('heroicon-o-clock')
                        ->modalHeading('Production Stage Log')
                        ->infolist(function (ProductionOrder $record): array {
                            return $record->productionStages->map(function ($stage) {
                                return Section::make(ucfirst($stage->stage))
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextEntry::make('Worker')->default(optional($stage->workforce)->name ?? 'Unassigned'),
                                            TextEntry::make('Status')->default($stage->status)->badge()->color(match ($stage->status) {
                                                'pending' => 'gray',
                                                'in_progress' => 'warning',
                                                'completed' => 'success',
                                                default => 'gray',
                                            }),
                                        ]),
                                    ]);
                            })->toArray();
                        })
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->visible(fn(ProductionOrder $record) => $record->status !== 'pending'),
                ])
                    ->icon('heroicon-m-ellipsis-horizontal')
                    ->color('info')
                    ->tooltip('Logistics'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductionOrders::route('/'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ProductionStats::class,
        ];
    }
}
