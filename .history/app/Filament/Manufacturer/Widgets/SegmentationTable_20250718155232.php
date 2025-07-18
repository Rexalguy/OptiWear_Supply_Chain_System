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
    
    protected int | string | array $columnSpan = 1;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                SegmentationResult::query()
                    ->selectRaw("
                        CONCAT(segment_label, '-', shirt_category) as id,
                        segment_label,
                        CONCAT(
                            'Customers in this segment prefer ',
                            shirt_category,
                            ' with ',
                            SUM(total_purchased),
                            ' purchases.'
                        ) as recommendation
                    ")
                    ->groupBy('segment_label', 'shirt_category')
                    ->orderBy('segment_label')
                    ->orderByDesc(DB::raw('SUM(total_purchased)'))
                    ->unique('se')
            )
            ->columns([
                Tables\Columns\TextColumn::make('segment_label')
                    ->label('Segment'),
                Tables\Columns\TextColumn::make('recommendation')
                    ->label('Recommendation')
                    ->wrap(),
            ]);
    }


}
