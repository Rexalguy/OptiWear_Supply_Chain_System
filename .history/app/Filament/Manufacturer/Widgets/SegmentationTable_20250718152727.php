<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;

use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class SegmentationTable extends BaseWidget
{
    protected static ?string $heading = 'Segment Recommendations';
    
    protected int | string | array $columnSpan = 1;

public function table(Table $table): Table
{
    // Build the query using the query builder (no Eloquent model)
    $query = DB::table('segmentation_results')
        ->select(
            'segment_label',
            DB::raw("
                CONCAT(
                    'Customers in this segment prefer ',
                    SUM(total_purchased),
                    ' purchases.'
                ) as recommendation
            "))
            ->groupBy('segment_label', 'shirt_category')
            ->orderBy('segment_label')
            ->orderByDesc(DB::raw('SUM(total_purchased)'))
            ->distinct();

        // Step 2: Return it to the widget
        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('segment_label')->label('Segment'),
                Tables\Columns\TextColumn::make('recommendation')->label('Recommendation')->wrap(),
            ]);
    }

}
