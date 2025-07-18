<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Widgets\TableWidget as BaseWidget;

class SegmentationTable extends BaseWidget
{
    protected static ?string $heading = 'Segment Recommendations';
    
    protected int | string | array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DB::table('segmentation_results')
                    ->select('segment_label', DB::raw("
                        CONCAT(
                            'Customers in this segment prefer ',
                            shirt_category,
                            ' with ',
                            SUM(total_purchased),
                            ' purchases.'
                        ) as recommendation
                    "))
                    ->groupBy('segment_label', 'shirt_category')
                    ->orderBy('segment_label')
                    ->orderByDesc(DB::raw('SUM(total_purchased)'))
                    ->distinct()
            )
            ->columns([
                Tables\Columns\TextColumn::make('segment_label')
                    ->label('Segment'),
                Tables\Columns\TextColumn::make('recommendation')
                    ->label('Recommendation')
                    ->wrap(), // to wrap long sentences
            ]);
    }

}
