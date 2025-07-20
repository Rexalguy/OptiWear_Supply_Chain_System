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
            ->columns([
                Tables\Columns\TextColumn::make('segment_label')
                    ->label('Segment'),
                Tables\Columns\TextColumn::make('recommendation')
                    ->label('Recommendation')
                    ->wrap(),
            ]);
    }

    // Fetch and transform the top shirt_category per segment
    protected function getTableRecords(): Collection
    {
        $raw = SegmentationResult::query()
            ->select('segment_label', 'shirt_category', DB::raw('SUM(total_purchased) as total'))
            ->groupBy('segment_label', 'shirt_category')
            ->get();

        return $raw->groupBy('segment_label')->map(function ($items, $segment) {
            $top = $items->sortByDesc('total')->first();
            $recommendation = match ($top->shirt_category) {
                'Sportswear' => "Sportswear is highly preferred in this segment with {$top->total} purchases.",
                'Casual' => "Casual wear dominates purchases in this segment with {$top->total} purchases.",
                'Formal' => "Formal shirts are favored in this segment with {$top->total} purchases.",
                default => "Customers in this segment prefer {$top->shirt_category} with {$top->total} purchases.",
            };

            return (object)[
                'id' => $segment . '-' . $top->shirt_category,
                'segment_label' => $segment,
                'recommendation' => $recommendation,
            ];
        })->values();
    }


}
