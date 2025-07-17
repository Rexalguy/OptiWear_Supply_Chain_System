<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class casualWearChart extends ChartWidget
{
    protected static ?string $heading = 'Casual Wear Demand Forecast';
    
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
        
        // Set date range based on filter
        if ($this->filter === '30_days') {
            $startDate = $today->copy()->addDay();
            $endDate = $today->copy()->addDays(30);
            $dateFormat = 'M d';
        } elseif ($this->filter === '12_months') {
            $startDate = $today->copy()->addDay();
            $endDate = $today->copy()->addMonths(12);
            $dateFormat = 'M Y';
        } else {
            $startDate = $today->copy()->addDay();
            $endDate = $today->copy()->addYears(5);
            $dateFormat = 'Y';
        }
        
        // Get casual wear data
        $results = DB::table('demand_prediction_results')
            ->where('time_frame', $this->filter)
            ->where('shirt_category', 'Casual')
            ->whereBetween('prediction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('prediction_date')
            ->get();
        
        // Process data based on timeframe
        if ($this->filter === '30_days') {
            // Daily data
            $labels = [];
            $data = [];
            
            foreach ($results as $result) {
                $labels[] = Carbon::parse($result->prediction_date)->format($dateFormat);
                $data[] = $result->predicted_quantity;
            }
        } elseif ($this->filter === '12_months') {
            // Monthly aggregation
            $monthlyData = [];
            foreach ($results as $result) {
                $month = Carbon::parse($result->prediction_date)->format('Y-m');
                if (!isset($monthlyData[$month])) {
                    $monthlyData[$month] = 0;
                }
                $monthlyData[$month] += $result->predicted_quantity;
            }
            
            $labels = [];
            $data = [];
            foreach ($monthlyData as $month => $quantity) {
                $labels[] = Carbon::parse($month . '-01')->format($dateFormat);
                $data[] = $quantity;
            }
        } else {
            // Yearly aggregation
            $yearlyData = [];
            foreach ($results as $result) {
                $year = Carbon::parse($result->prediction_date)->format('Y');
                if (!isset($yearlyData[$year])) {
                    $yearlyData[$year] = 0;
                }
                $yearlyData[$year] += $result->predicted_quantity;
            }
            
            $labels = [];
            $data = [];
            foreach ($yearlyData as $year => $quantity) {
                $labels[] = $year;
                $data[] = $quantity;
            }
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Casual Wear Predicted Quantity',
                    'data' => $data,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => '#36A2EB',
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
