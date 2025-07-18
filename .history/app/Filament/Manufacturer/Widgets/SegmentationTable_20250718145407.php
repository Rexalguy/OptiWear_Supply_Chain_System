<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class SegmentationTable extends BaseWidget
{
    protected static ?string $heading = 'Segment Percentage Contribution';
    
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('segment_label')
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

    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return null;
    }

    protected function getTableRecords(): \Illuminate\Support\Collection
    {
        return DB::table('segmentation_results')
            ->select([
                'segment_label',
                DB::raw('SUM(total_purchased) as total_quantity'),
                DB::raw('ROUND((SUM(total_purchased) * 100.0 / (SELECT SUM(total_purchased) FROM segmentation_results)), 1) as percentage_contribution')
            ])
            ->groupBy('segment_label')
            ->orderBy('percentage_contribution', 'desc')
            ->get();
    }
}
