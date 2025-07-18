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
        return $table
            ->query(\App\Models\User::query()->whereRaw('1 = 0'))
            ->columns([
                Tables\Columns\TextColumn::make('segment_label')
                    ->label('Segment')
                    ->weight('medium'),
                Tables\Columns\TextColumn::make('recommendation')
                    ->label('Recommendation')
                    ->wrap(),
            ])
            ->paginated(false);
    }

    public function getTableRecords(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\Paginator|\Illuminate\Contracts\Pagination\CursorPaginator
    {
        $segments = DB::table('segmentation_results')
            ->select('segment_label')
            ->groupBy('segment_label')
            ->orderBy('segment_label')
            ->get();

        // Add random 3-word recommendations
        $recommendations = [
            'Focus Premium Products',
            'Increase Loyalty Programs',
            'Target Young Adults',
            'Seasonal Marketing Campaigns',
            'Value Bundle Offers',
            'Exclusive Member Benefits',
            'Quality Over Quantity',
            'Social Media Engagement'
        ];

        $data = [];
        foreach($segments as $index => $segment) {
            $data[] = (object)[
                'segment_label' => $segment->segment_label,
                'recommendation' => $recommendations[$index] ?? 'Strategic Focus Areas'
            ];
        }

        return collect($data);
    }

    protected function getTableQuery(): ?\Illuminate\Database\Eloquent\Builder
    {
        return null;
    }
}
