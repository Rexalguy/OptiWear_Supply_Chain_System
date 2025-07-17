<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class formalWearChart extends ChartWidget
{
    protected static ?string $heading = 'Formal Wear Demand Forecast';

    public ?string $filter = '30_days';

    protected function getFilters(): ?array
    {
        return [
            '30_days' => 'Next 30 Days',
            '12_months' => 'Next 12 Months',
            '5_years' => 'Next 5 Years',
        ];
    }

    protected function getData(): array
    {
        $today = Carbon::today();

        // Use selected filter to get data for different time frames
        $timeFrame = $this->filter ?? '30_days';

        $allResults = DB::table('demand_prediction_results')
            ->where('time_frame', $timeFrame)
            ->orderBy('prediction_date')
            ->get();

        $totalRecords = $allResults->count();
        $categories = $allResults->pluck('shirt_category')->unique()->values();

        if ($totalRecords === 0) {
            return [
                'datasets' => [
                    [
                        'label' => 'Formal Wear (No Data - Sample)',
                        'data' => [10, 15, 20, 25, 30, 35, 40],
                        'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                        'borderColor' => '#FF6384',
                        'borderWidth' => 2,
                        'fill' => true,
                        'tension' => 0.4,
                    ],
                ],
                'labels' => ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5', 'Day 6', 'Day 7'],
            ];
        }

        // Filter for formal wear
        $results = $allResults->filter(function($result) {
            return stripos($result->shirt_category, 'formal') !== false;
        });

        // If no formal data, use first category available
        if ($results->isEmpty()) {
            $firstCategory = $categories->first();
            $results = $allResults->where('shirt_category', $firstCategory);
        }

        $labels = [];
        $data = [];

        // Group by month if time frame is 12_months or 5_years, else by day
        if ($timeFrame === '12_months' || $timeFrame === '5_years') {
            $grouped = $results->groupBy(function($item) {
                return Carbon::parse($item->prediction_date)->format('Y-m');
            });

            foreach ($grouped as $month => $items) {
                $labels[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
                $data[] = $items->sum('predicted_quantity');
            }
        } else {
            foreach ($results->take(30) as $result) {
                $labels[] = Carbon::parse($result->prediction_date)->format('M d');
                $data[] = (float) $result->predicted_quantity;
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Formal Wear Predicted Quantity',
                    'data' => $data,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => '#FF6384',
                    'borderWidth' => 2,
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
      