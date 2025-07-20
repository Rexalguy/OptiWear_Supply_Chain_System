<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class segmentationSummary extends BaseWidget
{
    protected static ?string $heading = 'Customer Segmentation Summary';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('segment_label')
                    ->label('Segment')
                    ->weight('medium')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('customer_count')
                    ->label('Customers')
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_purchased')
                    ->label('Total Revenue')
                    ->money('USD')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('avg_purchase')
                    ->label('Avg. Purchase')
                    ->money('USD')
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('contribution_percentage')
                    ->label('Revenue %')
                    ->formatStateUsing(fn ($state) => number_format($state, 1) . '%')
                    ->alignCenter()
                    ->color('success'),
                Tables\Columns\TextColumn::make('recommendations')
                    ->label('Strategy Recommendations')
                    ->wrap()
                    ->searchable(),
            ])
            ->striped()
            ->paginated(false);
    }

    protected function getTableQuery(): Builder
    {
        return DB::table('segmentation_results as sr')
            ->select([
                'sr.segment_label',
                DB::raw('COUNT(*) as customer_count'),
                DB::raw('SUM(sr.total_purchased) as total_purchased'),
                DB::raw('AVG(sr.total_purchased) as avg_purchase'),
                DB::raw('ROUND((SUM(sr.total_purchased) / (SELECT SUM(total_purchased) FROM segmentation_results WHERE total_purchased > 0) * 100), 1) as contribution_percentage'),
                DB::raw("CASE 
                    WHEN sr.segment_label = 'High Value Customers' THEN 'Focus on retention programs and premium product offerings'
                    WHEN sr.segment_label = 'Frequent Buyers' THEN 'Implement loyalty rewards and exclusive early access'
                    WHEN sr.segment_label = 'New Customers' THEN 'Onboarding campaigns and first-purchase incentives'
                    WHEN sr.segment_label = 'At-Risk Customers' THEN 'Re-engagement campaigns and special discounts'
                    WHEN sr.segment_label = 'Budget Conscious' THEN 'Value bundles and seasonal promotions'
                    WHEN sr.segment_label = 'Premium Shoppers' THEN 'Luxury product lines and VIP experiences'
                    WHEN sr.segment_label = 'Seasonal Buyers' THEN 'Targeted seasonal marketing and inventory planning'
                    ELSE 'General customer engagement strategies'
                END as recommendations")
            ])
            ->groupBy('sr.segment_label')
            ->having('total_purchased', '>', 0)
            ->orderByDesc('total_purchased')
            ->toBase();
    }
}