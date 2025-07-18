<?php

namespace App\Filament\Manufacturer\Widgets;

use App\Models\SegmentationResult;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class segmentationSummary extends BaseWidget
{
    protected static ?string $heading = 'Customer Segmentation Summary';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('segment_label')
                    ->label('Customer Segment')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('contribution')
                    ->label('Contribution')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state): string => number_format($state, 1) . '%'),

                TextColumn::make('comment')
                    ->label('Comment')
                    ->searchable()
                    ->wrap()
                    ->color('gray')
                    ->limit(80),
            ])
            ->defaultSort('contribution', 'desc')
            ->paginated([10, 25, 50])
            ->poll('30s')
            ->emptyStateHeading('No Segmentation Data')
            ->emptyStateDescription('Add customer segmentation data to view insights.');
    }

    public function getTableRecordKey($record): string
    {
        return (string) ($record->segment_label ?? uniqid());
    }

    protected function getTableQuery(): Builder
    {
        // Get total count for percentage calculation
        $totalCount = SegmentationResult::count();
        
        if ($totalCount == 0) {
            return SegmentationResult::query()->whereRaw('1 = 0');
        }

        // Debug: Log the total count
        \Log::info('Total count in segmentation_results: ' . $totalCount);

        // Let's check what the actual query returns
        $query = SegmentationResult::query()
            ->select([
                'segment_label',
                DB::raw('COUNT(*) as customer_count'),
                DB::raw('ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM segmentation_results)), 1) as contribution'),
                DB::raw('CASE 
                    WHEN segment_label LIKE "%High-Value%" OR segment_label LIKE "%Premium%" THEN "High spending customers who prefer quality over price. Focus on premium products and personalized service."
                    WHEN segment_label LIKE "%Frequent%" OR segment_label LIKE "%Regular%" THEN "Loyal customers with consistent purchasing patterns. Ideal for subscription models and loyalty programs."
                    WHEN segment_label LIKE "%Price-Sensitive%" OR segment_label LIKE "%Budget%" THEN "Cost-conscious buyers who respond well to discounts and value deals. Target with promotional campaigns."
                    WHEN segment_label LIKE "%Seasonal%" THEN "Purchase behavior tied to specific seasons or events. Plan targeted campaigns around peak periods."
                    WHEN segment_label LIKE "%Occasional%" OR segment_label LIKE "%Casual%" THEN "Infrequent buyers who need engagement strategies to increase purchase frequency."
                    WHEN segment_label LIKE "%Male%" THEN "Male customers in this age group showing specific purchasing patterns."
                    WHEN segment_label LIKE "%Female%" THEN "Female customers in this age group with distinct shopping preferences."
                    ELSE "Customer segment based on demographic data requiring targeted marketing strategies."
                END as comment')
            ])
            ->groupBy('segment_label')
            ->orderByDesc('contribution');

        // Debug: Log the actual SQL query
        \Log::info('SQL Query: ' . $query->toSql());
        
        // Debug: Log the actual results
        $results = $query->get();
        \Log::info('Query results: ', $results->toArray());

        return $query;
    }
}
