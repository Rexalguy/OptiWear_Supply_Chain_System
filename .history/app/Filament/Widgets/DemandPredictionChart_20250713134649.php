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

    protected static ?int $maxWidth = null; // Full width

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
        
        // Format dates for X axis based on time frame
        if ($this->timeFrame === '12_months') {
            // Group by month/year, aggregate predictions per month
            $dates = $results->pluck('prediction_date')->map(function($date) {
                return date('Y-m', strtotime($date));
            })->unique()->sort()->values();
            // Format for display as 'MMM YYYY'
            $displayDates = $dates->map(function($ym) {
                return date('M Y', strtotime($ym . '-01'));
            });
        } else {
            $dates = $results->pluck('prediction_date')->unique()->sort()->values();
            $displayDates = $dates;
        }

        $series = [];
        foreach ($categories as $category) {
            $data = [];
            if ($this->timeFrame === '12_months') {
                foreach ($dates as $ym) {
                    // Aggregate predicted_quantity for this category and month
                    $monthRows = $results->where('shirt_category', $category)
                        ->filter(function($row) use ($ym) {
                            return strpos($row->prediction_date, $ym) === 0;
                        });
                    $data[] = $monthRows->sum('predicted_quantity') ?: null;
                }
            } else {
                foreach ($dates as $date) {
                    $row = $results->where('shirt_category', $category)->where('prediction_date', $date)->first();
                    $data[] = ($row && isset($row->predicted_quantity)) ? (float) $row->predicted_quantity : null;
                }
            }
            $series[] = [
                'name' => $category,
                'data' => $data,
            ];
        }

        // Summaries (ignore rows without predicted_quantity)
        $validResults = $results->filter(fn($r) => isset($r->predicted_quantity));
        $max = $validResults->sortByDesc('predicted_quantity')->first();
        $min = $validResults->sortBy('predicted_quantity')->first();
        $total = $validResults->sum('predicted_quantity');

        $summary = '';
        if ($max && $min) {
            $summary = "Highest: {$max->shirt_category} ({$max->prediction_date}) - {$max->predicted_quantity}\n" .
                       "Lowest: {$min->shirt_category} ({$min->prediction_date}) - {$min->predicted_quantity}\n" .
                       "Total Predicted Demand: {$total}";
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 500, // Increased height for better axis visibility
            ],
            'series' => $series,
            'options' => [
                'xaxis' => [
                    'categories' => $displayDates->toArray(),
                    'title' => [
                        'text' => $this->timeFrame === '12_months' ? 'Month' : 'Date',
                        'style' => [
                            'fontWeight' => 600,
                            'fontSize' => '14px',
                        ],
                    ],
                ],
                'yaxis' => [
                    'title' => [
                        'text' => 'Predicted Demand',
                        'style' => [
                            'fontWeight' => 600,
                            'fontSize' => '14px',
                        ],
                    ],
                ],
                'toolbar' => [
                    'show' => true,
                ],
            ],
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
