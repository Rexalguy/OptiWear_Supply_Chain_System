<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class percentageContributionChart extends ChartWidget
{
    protected static ?string $heading = 'Category Contribution to Total Demand';
    
    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => 'Last 7 days',
            '30' => 'Last 30 days',
            '90' => 'Last 90 days',
            '365' => 'Last year',
        ];
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;
        
        // Get total demand for each category from the last X days
        $categoryData = DB::table('demand_prediction-')
            ->select('shirt_category', DB::raw('SUM(predicted_demand) as total_demand'))
            ->where('created_at', '>=', now()->subDays($days))
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
