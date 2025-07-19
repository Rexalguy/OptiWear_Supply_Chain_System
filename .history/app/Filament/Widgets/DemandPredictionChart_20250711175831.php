<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;

class DemandPredictionChart extends ApexChartWidget
{
    protected static ?string $chartId = 'demandPredictionChart';
    protected static ?string $heading = 'Demand Prediction (Line Chart)';

    protected function getOptions(): array
    {
        // Fetch demand prediction data from the database
        $results = DB::table('demand_prediction_results')
            ->orderBy('prediction_date')
            ->get();

        // Group by shirt category
        $categories = $results->pluck('shirt_category')->unique();
        $dates = $results->pluck('prediction_date')->unique()->sort()->values();

        $series = [];
        foreach ($categories as $category) {
            $data = [];
            foreach ($dates as $date) {
                $row = $results->where('shirt_category', $category)->where('prediction_date', $date)->first();
                $data[] = $row ? (float) $row->predicted_demand : null;
            }
            $series[] = [
                'name' => $category,
                'data' => $data,
            ];
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 350,
            ],
            'xaxis' => [
                'categories' => $dates->toArray(),
                'title' => ['text' => 'Date'],
            ],
            'yaxis' => [
                'title' => ['text' => 'Predicted Demand'],
            ],
            'series' => $series,
        ];
    }
}
