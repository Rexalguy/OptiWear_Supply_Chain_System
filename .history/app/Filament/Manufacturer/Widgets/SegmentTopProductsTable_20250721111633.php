<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\SegmentationResult;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Support\Facades\DB;

class SegmentTopProductsTable extends BaseWidget
{
    protected static ?string $heading = '🏆 ';
    
    protected static ?string $description = 'Top product category for the 8 most active customer segments (excluding segments with zero customers)';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTopProductsQuery())
            ->columns([
                TextColumn::make('segment_label')
                    ->label('Customer Segment')
                    ->badge()
                    ->color(fn (string $state): string => match(true) {
                        str_contains($state, 'Male') && str_contains($state, '18-25') => 'info',
                        str_contains($state, 'Male') && str_contains($state, '26-35') => 'primary',
                        str_contains($state, 'Male') && str_contains($state, '36-50') => 'success',
                        str_contains($state, 'Male') && str_contains($state, '51+') => 'warning',
                        str_contains($state, 'Female') && str_contains($state, '18-25') => 'danger',
                        str_contains($state, 'Female') && str_contains($state, '26-35') => 'gray',
                        str_contains($state, 'Female') && str_contains($state, '36-50') => 'indigo',
                        str_contains($state, 'Female') && str_contains($state, '51+') => 'purple',
                        default => 'secondary',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('top_product_category')
                    ->label('Top Product Category')
                    ->getStateUsing(fn ($record) => $record->top_product_category ?? $record->shirt_category)
                    ->badge()
                    ->color(fn (string $state): string => match($state) {
                        'Casual Wear' => 'info',
                        'Sports Wear', 'Sportswear' => 'success',
                        'Formal Wear' => 'primary',
                        'Work Wear', 'Workwear' => 'warning',
                        'Children Wear' => 'danger',
                        default => 'secondary',
                    })
                    ->icon(fn (string $state): string => match($state) {
                        'Casual Wear' => 'heroicon-m-user',
                        'Sports Wear', 'Sportswear' => 'heroicon-m-bolt',
                        'Formal Wear' => 'heroicon-m-briefcase',
                        'Work Wear', 'Workwear' => 'heroicon-m-wrench-screwdriver',
                        'Children Wear' => 'heroicon-m-heart',
                        default => 'heroicon-m-shopping-bag',
                    })
                    ->searchable(false) // Disable search for computed column
                    ->sortable(false), // Disable sort for computed column

                TextColumn::make('total_purchased')
                    ->label('Total Units Purchased')
                    ->numeric()
                    ->formatStateUsing(fn (int $state): string => number_format($state) . ' units')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('customer_count')
                    ->label('Customers in Segment')
                    ->numeric()
                    ->formatStateUsing(fn (int $state): string => number_format($state) . ' customers')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->defaultSort('total_purchased', 'desc')
            ->striped()
            ->paginated(false)
            ->searchable(false) // Disable global search to avoid SQL issues with computed columns
            ->emptyStateHeading('No segmentation data available')
            ->emptyStateDescription('Run the customer segmentation analysis to populate this table with top products by segment.')
            ->emptyStateIcon('heroicon-o-chart-bar-square')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }

    protected function getTopProductsQuery(): Builder
    {
        // First, get all segment data grouped by segment and category
        $segmentData = SegmentationResult::select([
            'segment_label',
            'gender', 
            'age_group',
            'shirt_category',
            DB::raw('SUM(total_purchased) as total_purchased'),
            DB::raw('SUM(customer_count) as customer_count')
        ])
        ->groupBy('segment_label', 'gender', 'age_group', 'shirt_category')
        ->havingRaw('SUM(total_purchased) > 0')
        ->havingRaw('SUM(customer_count) > 0') // Only segments with actual customers
        ->orderBy('segment_label')
        ->orderByDesc('total_purchased')
        ->get();

        // Get the top product for each segment (limit to 8 segments)
        $topProducts = $segmentData->groupBy('segment_label')->map(function ($products) {
            return $products->first(); // Gets the highest total_purchased for each segment
        })->take(8)->values(); // Limit to first 8 segments

        // If no data, return empty query
        if ($topProducts->isEmpty()) {
            return SegmentationResult::query()->whereRaw('1 = 0');
        }

        // Create a subquery to match these top products
        $conditions = $topProducts->map(function ($product) {
            return "(`segment_label` = '{$product->segment_label}' AND `shirt_category` = '{$product->shirt_category}')";
        })->implode(' OR ');

        return SegmentationResult::query()
            ->selectRaw("
                segment_label,
                gender,
                age_group,
                shirt_category as top_product_category,
                SUM(total_purchased) as total_purchased,
                SUM(customer_count) as customer_count,
                MIN(id) as id
            ")
            ->whereRaw($conditions)
            ->groupBy('segment_label', 'gender', 'age_group', 'shirt_category')
            ->havingRaw('SUM(customer_count) > 0') // Ensure we only show segments with customers
            ->orderByDesc('total_purchased')
            ->limit(8); // Limit to 8 rows maximum
    }
}
