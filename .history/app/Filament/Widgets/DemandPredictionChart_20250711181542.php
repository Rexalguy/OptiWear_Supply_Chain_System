<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Illuminate\Support\Facades\DB;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Widgets\Concerns\CanPoll;

class DemandPredictionChart extends ApexChartWidget
{
    use CanPoll;
    protected static ?string $chartId = 'demandPredictionChart';
    protected static ?string $heading = 'Demand Prediction (Line Chart)';

    public ?string $timeFrame = '30_days';

    protected function getFormSchema(): array
    {
        return [
            Select::make('timeFrame')
                ->label('Time Frame')
                ->options([
                    '30_days' => 'Next 30 Days',
                    '12_months' => 'Next 12 Months',
                    '5_years' => 'Next 5 Years',
                ])
                ->default('30_days')
                ->reactive(),
        ];
    }

    protected function getOptions(): array
    {
        // Fetch demand prediction data from the database
        $query = DB::table('demand_prediction_results');
        if ($this->timeFrame === '30_days') {
            $query->where('time_frame', '30_days');
        } elseif ($this->timeFrame === '12_months') {
            $query->where('time_frame', '12_months');
        } elseif ($this->timeFrame === '5_years') {
            $query->where('time_frame', '5_years');
        }
        $results = $query->orderBy('prediction_date')->get();

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

        // Summaries
        $max = $results->sortByDesc('predicted_demand')->first();
        $min = $results->sortBy('predicted_demand')->first();
        $total = $results->sum('predicted_demand');

        $summary = '';
        if ($max && $min) {
            $summary = "Highest: {$max->shirt_category} ({$max->prediction_date}) - {$max->predicted_demand}\n" .
                       "Lowest: {$min->shirt_category} ({$min->prediction_date}) - {$min->predicted_demand}\n" .
                       "Total Predicted Demand: {$total}";
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
            'annotations' => [],
            'summary' => $summary,
        ];
    }

    public function getFooter(): ?string
    {
        $options = $this->getOptions();
        return nl2br($options['summary'] ?? '');
    }
}
