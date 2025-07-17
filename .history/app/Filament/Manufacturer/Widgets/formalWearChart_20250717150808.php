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
        
        // Get all data to see what's available
        $allResults = DB::table('demand_prediction_results')
            ->where('time_frame', '30_days')
            ->get();
        
        // Check what we have
        $totalRecords = $allResults->count();
        $categories = $allResults->pluck('shirt_category')->unique()->values();
        
        // If no data, create sample data
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
        
        // Try to find formal data
        $results = $allResults->filter(function($result) {
            return stripos($result->shirt_category, 'formal') !== false;
        });
        
        // If no formal data, use first category available
        if ($results->isEmpty()) {
            $firstCategory = $categories->first();
            $results = $allResults->where('shirt_category', $firstCategory)->take(10);
        }
        
        $labels = [];
        $data = [];
        
        foreach ($results->take(10) as $result) {
            $labels[] = Carbon::parse($result->prediction_date)->format('M d');
            $data[] = (float) $result->predicted_quantity;
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Formal Wear (' . $results->count() . ' records, Categories: ' . $categories->implode(', ') . ')',
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
      