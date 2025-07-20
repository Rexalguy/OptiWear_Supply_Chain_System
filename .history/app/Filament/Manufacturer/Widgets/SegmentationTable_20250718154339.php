<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;

use App\Models\SegmentationResult;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class SegmentationTable extends BaseWidget
{
    protected static ?string $heading = 'Segment Recommendations';
    
    protected int | string | array $columnSpan = 1;    public function table(Table $table): Table
    {
        $subquery = DB::table('segmentation_results as sr1')
            ->selectRaw('
                sr1.segment_label,
                sr1.shirt_category,
                SUM(sr1.total_purchased) as total_purchased
            ')
            ->groupBy('sr1.segment_label', 'sr1.shirt_category');

        $topPerSegment = DB::table(DB::raw("({$subquery->toSql()}) as ranked"))
            ->mergeBindings($subquery)
            ->selectRaw("
                CONCAT(segment_label, '-', shirt_category) as id,
                segment_label,
                shirt_category,
                total_purchased,
                CASE 
                    WHEN shirt_category = 'Sportswear' THEN CONCAT('Sportswear is highly preferred in this segment with ', total_purchased, ' purchases.')
                    WHEN shirt_category = 'Casual' THEN CONCAT('Casual wear dominates purchases in this segment with ', total_purchased, ' purchases.')
                    WHEN shirt_category = 'Formal' THEN CONCAT('Formal shirts are favored in this segment with ', total_purchased, ' purchases.')
                    ELSE CONCAT('Customers in this segment prefer ', shirt_category, ' with ', total_purchased, ' purchases.')
                END as recommendation
            ")
            ->join(DB::raw("
                (
                    SELECT segment_label, MAX(total_purchased) as max_total
                    FROM (
                        SELECT segment_label, shirt_category, SUM(total_purchased) as total_purchased
                        FROM segmentation_results
                        GROUP BY segment_label, shirt_category
                    ) as inner_summary
                    GROUP BY segment_label
                ) as max_per_segment
            "), function($join) {
                $join->on('ranked.segment_label', '=', 'max_per_segment.segment_label')
                     ->on('ranked.total_purchased', '=', 'max_per_segment.max_total');
            });

        return $table
            ->query(function () use ($topPerSegment) {
                // Use Eloquent's fromSub to wrap the query builder as a subquery
                return SegmentationResult::query()
                    ->fromSub($topPerSegment, 'segmentation_view')
                    ->select('*');
            })
            ->columns([
                Tables\Columns\TextColumn::make('segment_label')
                    ->label('Segment'),
                Tables\Columns\TextColumn::make('recommendation')
                    ->label('Recommendation')
                    ->wrap(),
            ]);
    }


}
