<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class percentageContributionChart extends ChartWidget
{
    protected static ?string $heading = 'Category Contribution to Total Demand';
    
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
        // Set date range based on filter to match line graphs
        $today = \Carbon\Carbon::today();
        
        if ($this->filter === '30_days') {
            $startDate = $today->copy()->addDay();
            $endDate = $today->copy()->addDays(30);
        } elseif ($this->filter === '12_months') {
            $startDate = $today->copy()->addDay();
            $endDate = $today->copy()->addMonths(12);
        } else { // 5_years
            $startDate = $today->copy()->addDay();
            $endDate = $today->copy()->addYears(5);
        }
        
        // Get total demand for each category from the prediction results
        $categoryData = DB::table('demand_prediction_results')
            ->select('shirt_category', DB::raw('SUM(predicted_quantity) as total_demand'))
            ->where('time_frame', $this->filter)
            ->whereBetween('prediction_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->groupBy('shirt_category')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColor = [
            'rgba(255, 99, 132, 0.8)',   // Red
            'rgba(54, 162, 235, 0.8)',   // Blue  
            'rgba(255, 205, 86, 0.8)',   // Yellow
            'rgba(75, 192, 192, 0.8)',   // Green
            'rgba(153, 102, 255, 0.8)',  // Purple
        ];

        foreach ($categoryData as $index => $category) {
            $labels[] = ucfirst(str_replace('_', ' ', $category->shirt_category));
            $data[] = (float) $category->total_demand;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Demand Contribution',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColor, 0, count($data)),
                    'borderWidth' => 2,
                    'borderColor' => 'rgba(255, 255, 255, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'r' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'display' => true,
                    ],
                    'grid' => [
                        'color' => 'rgba(255, 255, 255, 0.1)',
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                    ],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ": " + context.parsed.toLocaleString() + " units (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
        ];
    }
}
