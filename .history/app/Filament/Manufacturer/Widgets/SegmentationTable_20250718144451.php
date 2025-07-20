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

    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        // Get the segment data with percentage contribution
        $segmentData = DB::table('segmentation_results')
            ->select([
                'segment_label as segment',
                DB::raw('SUM(total_purchased) as total_quantity'),
                DB::raw('ROUND((SUM(total_purchased) * 100.0 / (SELECT SUM(total_purchased) FROM segmentation_results)), 1) as percentage_contribution')
            ])
            ->groupBy('segment_label')
            ->orderBy('percentage_contribution', 'desc')
            ->get();

        // Convert to a format that the table can use
        // Since we can't return a collection directly, we'll use a workaround
        // by creating a temporary model or using DB::table with a subquery
        
        // For now, let's use a raw query approach
        return DB::table(DB::raw('(
            SELECT 
                segment_label as segment,
                ROUND((SUM(total_purchased) * 100.0 / (SELECT SUM(total_purchased) FROM segmentation_results)), 1) as percentage_contribution
            FROM segmentation_results
            GROUP BY segment_label
            ORDER BY percentage_contribution DESC
        ) as segment_stats'))
            ->toBase();
    }
}
