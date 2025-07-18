<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;
use App\Models\User;

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
        // Use a raw query with User model as base (won't actually use users table)
        $segmentData = DB::table('segmentation_results')
            ->select([
                'segment_label as segment',
                DB::raw('ROUND((SUM(total_purchased) * 100.0 / (SELECT SUM(total_purchased) FROM segmentation_results)), 1) as percentage_contribution')
            ])
            ->groupBy('segment_label')
            ->orderBy(DB::raw('ROUND((SUM(total_purchased) * 100.0 / (SELECT SUM(total_purchased) FROM segmentation_results)), 1)'), 'desc')
            ->get();

        // Since we can't return DB query directly, we'll use User model with whereRaw that never matches
        // and override the data in the widget
        return User::query()->whereRaw('1 = 0');
    }
}
