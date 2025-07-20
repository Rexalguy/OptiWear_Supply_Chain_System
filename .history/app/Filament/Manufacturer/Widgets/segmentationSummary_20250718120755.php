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
                // Create a custom query that returns models with proper keys
                return SegmentationResult::query()
                    ->selectRaw('
                        id,
                        segment_label,
                        COUNT(*) as customer_count,
                        SUM(total_purchased) as total_revenue,
                        AVG(total_purchased) as avg_purchase_value,
                        MAX(shirt_category) as shirt_category,
                        MAX(age_group) as age_group,
                        "Mixed" as gender_distribution
                    ')
                    ->groupBy('id', 'segment_label')
                    ->orderBy('total_revenue', 'desc');
            } else {
                // Return a proper Eloquent query with fallback data
                return $this->getFallbackQuery();
            }
        } catch (\Exception $e) {
            return $this->getFallbackQuery();
        }
    }

    protected function getFallbackQuery(): Builder
    {
        // Return an empty query since we don't have real data
        // The table will show "No records found" which is appropriate
        return SegmentationResult::query()->whereRaw('1 = 0');
    }
}
