<?php

namespace App\Filament\Manufacturer\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SegmentationPolarChart extends ChartWidget
{
    protected static ?string $heading = 'Customer Segmentation Overview';

    protected static ?string $maxHeight = '400px';
    
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // Query to get customer count by segment_label
        $segmentData = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('SUM(customer_count) as total_customers'))
            ->groupBy('segment_label')
            ->orderBy('total_customers', 'desc')
            ->get();

        $labels = $segmentData->pluck('segment_label')->toArray();
        $data = $segmentData->pluck('total_customers')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Customers',
                    'data' => $data,

    protected function getData(): array
    {
        // Query to get customer count by segment_label
        $segmentData = DB::table('segmentation_results')
            ->select('segment_label', DB::raw('COUNT(*) as customer_count'))
            ->groupBy('segment_label')
            ->orderBy('customer_count', 'desc')
            ->get();

        $labels = $segmentData->pluck('segment_label')->toArray();
        $data = $segmentData->pluck('customer_count')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Number of Customers',
                    'data' => $data,
                    'backgroundColor' => [
                        '#FF6384', // Pink/Red
                        '#36A2EB', // Blue
                        '#FFCE56', // Yellow
                        '#4BC0C0', // Teal
                        '#9966FF', // Purple
                        '#FF9F40', // Orange
                        '#EF4444', // Red
                        '#C9CBCF', // Grey
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'polarArea';
    }
}
