<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AgeGroupPurchasePatternChart extends ChartWidget
{
    protected static ?string $heading = 'Purchase Patterns by Age Group and Gender';
    
    protected function getData(): array
    {
        // Get data grouped by age group and gender
        $ageGroupData = DB::table('segmentation_results')
            ->select('age_group', 'gender', DB::raw('AVG(total_purchased) as avg_purchases'))
            ->groupBy('age_group', 'gender')
            ->orderBy('age_group')
            ->get();

        $ageGroups = $ageGroupData->pluck('age_group')->unique()->sort()->values();
        $genders = $ageGroupData->pluck('gender')->unique()->values();

        $labels = $ageGroups->toArray();
        $datasets = [];

        $colors = [
            'Male' => 'rgba(54, 162, 235, 0.7)',    // Blue
            'Female' => 'rgba(255, 99, 132, 0.7)',  // Pink
        ];

        foreach ($genders as $gender) {
            $data = [];
            
            foreach ($ageGroups as $ageGroup) {
                $avgPurchases = $ageGroupData
                    ->where('age_group', $ageGroup)
                    ->where('gender', $gender)
                    ->avg('avg_purchases');
                
                $data[] = $avgPurchases ? round($avgPurchases, 1) : 0;
            }

            $datasets[] = [
                'label' => $gender,
                'data' => $data,
                'backgroundColor' => $colors[$gender] ?? 'rgba(75, 192, 192, 0.7)',
                'borderColor' => str_replace('0.7', '1', $colors[$gender] ?? 'rgba(75, 192, 192, 1)'),
                'borderWidth' => 2,
                'fill' => false,
                'tension' => 0.4,
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Average Purchases'
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Age Groups'
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
            ],
        ];
    }
}
