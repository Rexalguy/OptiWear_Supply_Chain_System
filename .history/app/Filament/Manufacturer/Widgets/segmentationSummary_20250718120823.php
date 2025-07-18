<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use App\Models\SegmentationResult;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
                    ->formatStateUsing(fn (string $state): string => $state . '%'),

                TextColumn::make('comment')
                    ->label('Comment')
                    ->searchable()
                    ->wrap()
                    ->color('gray')
                    ->limit(80),
            ])
            ->defaultSort('total_revenue', 'desc')
            ->paginated([10, 25, 50])
            ->poll('30s');
    }

    public function getTableRecordKey($record): string
    {
        // Handle both Eloquent models and stdClass objects
        if ($record instanceof Model) {
            return (string) ($record->getKey() ?? $record->id ?? $record->segment_label ?? uniqid());
        }
        
        if (is_object($record)) {
            return (string) ($record->id ?? $record->segment_label ?? uniqid());
        }
        
        // Fallback for arrays or other types
        if (is_array($record)) {
            return (string) ($record['id'] ?? $record['segment_label'] ?? uniqid());
        }
        
        return (string) uniqid();
    }

    protected function getTableQuery(): Builder
    {
        try {
            // Check if we have data in the segmentation_results table
            $hasData = SegmentationResult::exists();
            
            if ($hasData) {
                // Get total customer count for percentage calculation
                $totalCustomers = SegmentationResult::count();
                
                // Create a custom query that returns segments with contribution and comments
                return SegmentationResult::query()
                    ->selectRaw('
                        id,
                        segment_label,
                        ROUND((COUNT(*) * 100.0 / ?), 1) as contribution,
                        CASE 
                            WHEN segment_label LIKE "%High-Value%" OR segment_label LIKE "%Premium%" THEN "High spending customers who prefer quality over price. Focus on premium products and personalized service."
                            WHEN segment_label LIKE "%Frequent%" OR segment_label LIKE "%Regular%" THEN "Loyal customers with consistent purchasing patterns. Ideal for subscription models and loyalty programs."
                            WHEN segment_label LIKE "%Price-Sensitive%" OR segment_label LIKE "%Budget%" THEN "Cost-conscious buyers who respond well to discounts and value deals. Target with promotional campaigns."
                            WHEN segment_label LIKE "%Seasonal%" THEN "Purchase behavior tied to specific seasons or events. Plan targeted campaigns around peak periods."
                            WHEN segment_label LIKE "%Occasional%" OR segment_label LIKE "%Casual%" THEN "Infrequent buyers who need engagement strategies to increase purchase frequency."
                            ELSE "General customer segment requiring further analysis for behavioral insights."
                        END as comment
                    ', [$totalCustomers])
                    ->groupBy('id', 'segment_label')
                    ->orderBy('contribution', 'desc');
            } else {
                // Return fallback data with sample segments
                return $this->getFallbackQueryWithSampleData();
            }
        } catch (\Exception $e) {
            return $this->getFallbackQueryWithSampleData();
        }
    }

    protected function getFallbackQuery(): Builder
    {
        // Return an empty query since we don't have real data
        // The table will show "No records found" which is appropriate
        return SegmentationResult::query()->whereRaw('1 = 0');
    }
}
