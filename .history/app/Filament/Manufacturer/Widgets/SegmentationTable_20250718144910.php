<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class SegmentationTable extends BaseWidget
{
    protected static ?string $heading = 'Segment Percentage Contribution';
    
    protected int | string | array $columnSpan = 1;

    protected function getTableData(): Collection
    {
        // Get the segment data with percentage contribution
        return DB::table('segmentation_results')
            ->select([
                'segment_label as segment',
                DB::raw('ROUND((SUM(total_purchased) * 100.0 / (SELECT SUM(total_purchased) FROM segmentation_results)), 1) as percentage_contribution')
            ])
            ->groupBy('segment_label')
            ->orderBy(DB::raw('ROUND((SUM(total_purchased) * 100.0 / (SELECT SUM(total_purchased) FROM segmentation_results)), 1)'), 'desc')
            ->get();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn() => null) // No query needed, we'll use getTableRecords instead
            ->columns([
                Tables\Columns\TextColumn::make('segment')
                    ->label('Segment')
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('percentage_contribution')
                    ->label('Percentage Contribution')
                    ->formatStateUsing(fn ($state) => number_format($state, 1) . '%')
                    ->alignEnd()
                    ->color('success'),
            ])
            ->paginated(false);
    }

    protected function getTableRecords(): Collection
    {
        return $this->getTableData();
    }

    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return null;
    }
}
